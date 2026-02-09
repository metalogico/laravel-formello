<?php

namespace Tests\Unit;

use Orchestra\Testbench\TestCase;
use Metalogico\Formello\Formello;
use Metalogico\Formello\FormelloField;
use Metalogico\Formello\Widgets\ColorWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ViewErrorBag;

class ColorWidgetTest extends TestCase
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

    private function makeFormWithColorWidget()
    {
        return new class($this->makeDummyModel(), new ViewErrorBag()) extends Formello {
            protected function fields(): array {
                return [
                    FormelloField::make('field')->widget('color'),
                ];
            }
            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };
    }

    public function test_color_widget_is_instantiated_and_renders()
    {
        $form = $this->makeFormWithColorWidget();
        $fields = $form->getFields();
        $this->assertArrayHasKey('field', $fields);
        $this->assertInstanceOf(ColorWidget::class, $fields['field']['widget']);
        $output = $form->renderField('field');
        $this->assertIsString($output);
    }
}
