<?php

namespace Tests\Unit;

use Metalogico\Formello\Formello;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\ViewErrorBag;
use Metalogico\Formello\FormelloField;
use Illuminate\Database\Eloquent\Model;
use Metalogico\Formello\Widgets\Select2Widget;

class Select2WidgetTest extends TestCase
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

    private function makeFormWithSelect2Widget()
    {
        return new class($this->makeDummyModel(), new ViewErrorBag()) extends Formello {
            protected function fields(): array {
                return [
                    FormelloField::make('field')
                        ->widget(Select2Widget::class, ['choices' => ['a' => 'A', 'b' => 'B']])
                ];
            }
            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };
    }

    public function test_select2_widget_is_instantiated_and_renders()
    {
        $form = $this->makeFormWithSelect2Widget();
        $fields = $form->getFields();
        $this->assertArrayHasKey('field', $fields);
        $this->assertInstanceOf(Select2Widget::class, $fields['field']['widget']);
        $output = $form->renderField('field');
        $this->assertIsString($output);
    }
}
