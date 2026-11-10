<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Metalogico\Formello\Formello;
use Metalogico\Formello\FormelloField;
use Metalogico\Formello\Http\Controllers\FormelloComputeController;
use Metalogico\Formello\Support\FormelloState;
use Orchestra\Testbench\TestCase;

class FormelloComputeControllerTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Metalogico\Formello\FormelloServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('formello.css_framework', 'bootstrap5');
        $app['config']->set('formello.reactive.compute_path', '/formello/compute');
        // Feature tests exercise the controller without forcing a full auth stack
        // unless a test opts into the default middleware.
        $app['config']->set('formello.reactive.middleware', ['web']);
        $app['config']->set('formello.reactive.allowed_forms', [
            ComputeTestForm::class,
        ]);
        $app['config']->set('formello.reactive.authorize_model', null);
        $app['config']->set('auth.providers.users.model', User::class);
    }

    protected function defineDatabaseMigrations()
    {
        Schema::create('users', function ($table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->timestamps();
        });

        Schema::create('compute_items', function ($table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    protected function computePayload(array $overrides = []): array
    {
        return array_merge([
            'form_class' => ComputeTestForm::class,
            'changed_field' => 'region_id',
            'form_data' => [
                'region_id' => '1',
                'province_id' => null,
            ],
        ], $overrides);
    }

    public function test_compute_happy_path_invokes_server_callback(): void
    {
        $response = $this->postJson(route('formello.compute'), $this->computePayload());

        $response->assertOk()
            ->assertJson([
                'values' => [
                    'province_id' => null,
                ],
                'options' => [
                    'province_id' => [
                        '10' => 'Province A',
                        '11' => 'Province B',
                    ],
                ],
            ]);
    }

    public function test_empty_allowed_forms_is_fail_closed(): void
    {
        config(['formello.reactive.allowed_forms' => []]);

        $this->postJson(route('formello.compute'), $this->computePayload())
            ->assertForbidden()
            ->assertJson(['error' => 'Form class not allowed']);
    }

    public function test_star_allows_any_formello_subclass(): void
    {
        config(['formello.reactive.allowed_forms' => ['*']]);

        $this->postJson(route('formello.compute'), $this->computePayload())
            ->assertOk();
    }

    public function test_form_not_in_whitelist_is_forbidden(): void
    {
        config(['formello.reactive.allowed_forms' => [
            'App\\Forms\\SomeOtherForm',
        ]]);

        $this->postJson(route('formello.compute'), $this->computePayload())
            ->assertForbidden()
            ->assertJson(['error' => 'Form class not allowed']);
    }

    public function test_non_formello_class_is_forbidden(): void
    {
        config(['formello.reactive.allowed_forms' => ['*']]);

        $this->postJson(route('formello.compute'), $this->computePayload([
            'form_class' => NotAFormelloClass::class,
        ]))
            ->assertForbidden()
            ->assertJson(['error' => 'Form class is invalid']);
    }

    public function test_missing_form_class_returns_not_found(): void
    {
        config(['formello.reactive.allowed_forms' => ['*']]);

        $this->postJson(route('formello.compute'), $this->computePayload([
            'form_class' => 'Tests\\Feature\\DoesNotExistForm',
        ]))
            ->assertNotFound()
            ->assertJson(['error' => 'Form class not found']);
    }

    public function test_validation_errors_when_payload_incomplete(): void
    {
        $this->postJson(route('formello.compute'), [])
            ->assertStatus(422);
    }

    public function test_field_without_server_callback_returns_empty_json(): void
    {
        $this->postJson(route('formello.compute'), $this->computePayload([
            'changed_field' => 'name',
        ]))
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_authorize_model_callback_can_deny(): void
    {
        config([
            'formello.reactive.allowed_forms' => ['*'],
            'formello.reactive.authorize_model' => fn ($request, $model) => false,
        ]);

        $item = ComputeItem::query()->create(['name' => 'secret']);

        $this->postJson(route('formello.compute'), $this->computePayload([
            'model_class' => ComputeItem::class,
            'model_id' => $item->id,
        ]))
            ->assertForbidden()
            ->assertJson(['error' => 'Model not authorized']);
    }

    public function test_authorize_model_callback_can_allow(): void
    {
        config([
            'formello.reactive.allowed_forms' => ['*'],
            'formello.reactive.authorize_model' => fn ($request, $model) => true,
        ]);

        $item = ComputeItem::query()->create(['name' => 'ok']);

        $this->postJson(route('formello.compute'), $this->computePayload([
            'model_class' => ComputeItem::class,
            'model_id' => $item->id,
        ]))
            ->assertOk();
    }

    public function test_auth_middleware_rejects_guests(): void
    {
        Route::post('/formello/compute-auth', [FormelloComputeController::class, 'handle'])
            ->middleware(['web', 'auth']);

        $this->postJson('/formello/compute-auth', $this->computePayload())
            ->assertUnauthorized();
    }

    public function test_non_eloquent_model_class_is_forbidden(): void
    {
        $this->postJson(route('formello.compute'), $this->computePayload([
            'model_class' => NotAFormelloClass::class,
            'model_id' => 1,
        ]))
            ->assertForbidden()
            ->assertJson(['error' => 'Model not authorized']);
    }
}

class ComputeTestForm extends Formello
{
    protected function fields(): array
    {
        return [
            FormelloField::make('name')->widget('text'),
            FormelloField::make('region_id')
                ->widget('tomselect')
                ->reactive(['server' => 'onRegionChanged']),
            FormelloField::make('province_id')->widget('tomselect'),
        ];
    }

    protected function create(): array
    {
        return [];
    }

    protected function edit(): array
    {
        return [];
    }

    public function onRegionChanged(FormelloState $state): void
    {
        $state->setOptions('province_id', [
            '10' => 'Province A',
            '11' => 'Province B',
        ]);
        $state->set('province_id', null);
    }
}

class NotAFormelloClass
{
}

class ComputeItem extends Model
{
    protected $table = 'compute_items';

    protected $guarded = [];
}
