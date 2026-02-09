<?php

namespace Tests\Unit;

use Orchestra\Testbench\TestCase;
use Metalogico\Formello\Formello;
use Metalogico\Formello\FormelloField;
use Metalogico\Formello\Widgets\TextWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ViewErrorBag;

class TextWidgetTest extends TestCase
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
        return new class extends Model {
            public function getTable() { return 'dummy'; }
        };
    }

    private function makeFormWithTextWidget()
    {
        return new class($this->makeDummyModel(), new ViewErrorBag()) extends Formello {
            protected function fields(): array {
                return [
                    FormelloField::make('field')->widget('text'),
                ];
            }
            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };
    }

    public function test_text_widget_is_instantiated_and_renders()
    {
        $form = $this->makeFormWithTextWidget();
        $fields = $form->getFields();
        $this->assertArrayHasKey('field', $fields);
        $this->assertInstanceOf(TextWidget::class, $fields['field']['widget']);
        $output = $form->renderField('field');
        $this->assertIsString($output);
    }

    public function test_text_widget_renders_with_icon()
    {
        $form = new class($this->makeDummyModel(), new ViewErrorBag()) extends Formello {
            protected function fields(): array {
                return [
                    FormelloField::make('field_with_icon')
                        ->widget('text')
                        ->icon('<i class="fa-solid fa-user"></i>'),
                ];
            }
            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };

        $output = $form->renderField('field_with_icon');

        $this->assertStringContainsString('<div class="input-group">', $output);
        $this->assertStringContainsString('<i class="fa-solid fa-user"></i>', $output);
    }
}
