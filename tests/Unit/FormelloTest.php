<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ViewErrorBag;
use Metalogico\Formello\Formello;
use Metalogico\Formello\FormelloField;
use Orchestra\Testbench\TestCase;

class FormelloTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('formello.css_framework', 'bootstrap5');
    }

    protected function getPackageProviders($app)
    {
        return [
            \Metalogico\Formello\FormelloServiceProvider::class,
        ];
    }

    private function makeDummyModel()
    {
        return new class extends Model
        {
            public function getTable()
            {
                return 'dummy';
            }
        };
    }

    public function test_formello_can_be_instantiated_and_renders()
    {
        $form = new class($this->makeDummyModel(), new ViewErrorBag) extends Formello
        {
            protected function fields(): array
            {
                return [
                    FormelloField::make('field')
                        ->label('Test')
                        ->widget('text'),
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
        $this->assertInstanceOf(Formello::class, $form);
        try {
            $output = $form->render();
            $this->assertIsString($output);
        } catch (\Throwable $e) {
            $this->fail('Render exception: '.$e->getMessage()."\n".$e->getTraceAsString());
        }
    }

    public function test_is_creating_returns_true_for_new_model()
    {
        $model = $this->makeDummyModel();

        $form = new class($model, new ViewErrorBag) extends Formello
        {
            protected function fields(): array
            {
                return [];
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

        $this->assertTrue($form->isCreating());
        $this->assertFalse($form->isEditing());
    }

    public function test_is_editing_returns_true_for_existing_model()
    {
        $model = $this->makeDummyModel();
        $model->exists = true; // Simulate an existing model

        $form = new class($model, new ViewErrorBag) extends Formello
        {
            protected function fields(): array
            {
                return [];
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

        $this->assertTrue($form->isEditing());
        $this->assertFalse($form->isCreating());
    }
}
