<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Metalogico\Formello\Formello;
use Metalogico\Formello\FormelloField;
use Metalogico\Formello\Widgets\TextWidget;
use Orchestra\Testbench\TestCase;

class ValidationRepopulateTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('formello.css_framework', 'bootstrap5');
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    }

    protected function getPackageProviders($app)
    {
        return [
            \Metalogico\Formello\FormelloServiceProvider::class,
        ];
    }

    private function makeDummyModel(): Model
    {
        return new class extends Model
        {
            public $email;

            public $password;

            public function getTable()
            {
                return 'dummy';
            }
        };
    }

    private function flashOld(array $input): void
    {
        $this->app['session']->start();
        $this->app['session']->flashInput($input);
        $this->app['request']->setLaravelSession($this->app['session']->driver());
    }

    private function errorsFor(string $field): ViewErrorBag
    {
        $bag = new ViewErrorBag;
        $bag->put('default', new MessageBag([$field => ['The field is required.']]));

        return $bag;
    }

    public function test_toggle_uses_old_input_for_checked_state(): void
    {
        $this->flashOld(['active' => '1']);

        $model = $this->makeDummyModel();
        $model->setAttribute('active', 0);

        $form = new class($model, $this->errorsFor('active')) extends Formello
        {
            protected function fields(): array
            {
                return [
                    FormelloField::make('active')->widget('toggle')->label('Active'),
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
        };

        $html = $form->renderField('active');
        $this->assertMatchesRegularExpression('/name="active"[^>]*checked/', $html);
    }

    public function test_toggle_unchecked_old_input_is_not_checked(): void
    {
        $this->flashOld(['active' => '0']);

        $form = new class($this->makeDummyModel(), $this->errorsFor('active')) extends Formello
        {
            protected function fields(): array
            {
                return [
                    FormelloField::make('active')->widget('toggle')->value(true)->label('Active'),
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
        };

        $html = $form->renderField('active');
        $this->assertDoesNotMatchRegularExpression('/name="active"[^>]*checked/', $html);
    }

    public function test_select_multiple_repopulates_from_old_input(): void
    {
        $this->flashOld(['tags' => ['b']]);

        $form = new class($this->makeDummyModel(), $this->errorsFor('tags')) extends Formello
        {
            protected function fields(): array
            {
                return [
                    FormelloField::make('tags')
                        ->widget('select')
                        ->multiple()
                        ->choices(['a' => 'Alpha', 'b' => 'Beta']),
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
        };

        $html = $form->renderField('tags');
        $this->assertStringContainsString('name="tags[]"', $html);
        $this->assertMatchesRegularExpression('/value="b"\s+selected/', $html);
        $this->assertDoesNotMatchRegularExpression('/value="a"\s+selected/', $html);
    }

    public function test_checkboxes_use_unique_ids_and_old_input(): void
    {
        $this->flashOld(['tags' => ['b']]);

        $form = new class($this->makeDummyModel(), $this->errorsFor('tags')) extends Formello
        {
            protected function fields(): array
            {
                return [
                    FormelloField::make('tags')
                        ->widget('checkboxes')
                        ->choices(['a' => 'Alpha', 'b' => 'Beta']),
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
        };

        $html = $form->renderField('tags');
        $this->assertStringContainsString('id="tags_a"', $html);
        $this->assertStringContainsString('id="tags_b"', $html);
        $this->assertStringNotContainsString('id="tags"', $html);
        $this->assertStringContainsString('value="b"', $html);
        $this->assertMatchesRegularExpression('/value="b"[^>]*checked|checked[^>]*value="b"/', $html);
    }

    public function test_checkboxes_without_choices_render_empty(): void
    {
        $form = new class($this->makeDummyModel(), new ViewErrorBag) extends Formello
        {
            protected function fields(): array
            {
                return [
                    FormelloField::make('tags')->widget('checkboxes'),
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
        };

        $html = $form->renderField('tags');
        $this->assertIsString($html);
        $this->assertStringNotContainsString('type="checkbox"', $html);
    }

    public function test_schema_inspector_email_and_password_set_input_type(): void
    {
        $form = new class($this->makeDummyModel(), new ViewErrorBag) extends Formello
        {
            protected function fields(): array
            {
                return [
                    FormelloField::make('email')->label('Email'),
                    FormelloField::make('password')->label('Password'),
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
        };

        $fields = $form->getFields();
        $this->assertInstanceOf(TextWidget::class, $fields['email']['widget']);
        $this->assertInstanceOf(TextWidget::class, $fields['password']['widget']);
        $this->assertSame('email', $fields['email']['config']['type']);
        $this->assertSame('password', $fields['password']['config']['type']);

        $emailHtml = $form->renderField('email');
        $passwordHtml = $form->renderField('password');
        $this->assertStringContainsString('type="email"', $emailHtml);
        $this->assertStringContainsString('type="password"', $passwordHtml);
    }

    public function test_upload_bootstrap_does_not_set_value_attribute(): void
    {
        $form = new class($this->makeDummyModel(), new ViewErrorBag) extends Formello
        {
            protected function fields(): array
            {
                return [
                    FormelloField::make('file')->widget('upload')->value('secret.png'),
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
        };

        $html = $form->renderField('file');
        $this->assertStringNotContainsString('value="secret.png"', $html);
        $this->assertStringContainsString('type="file"', $html);
    }
}
