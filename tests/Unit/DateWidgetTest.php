<?php

namespace Tests\Unit;

use Metalogico\Formello\Formello;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\ViewErrorBag;
use Metalogico\Formello\FormelloField;
use Illuminate\Database\Eloquent\Model;
use Metalogico\Formello\Widgets\DateWidget;

class DateWidgetTest extends TestCase
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

    private function makeFormWithDateWidget()
    {
        return new class($this->makeDummyModel(), new ViewErrorBag()) extends Formello {
            protected function fields(): array {
                return [
                    FormelloField::make('field')
                        ->widget(DateWidget::class)
                ];
            }
            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };
    }

    public function test_date_widget_is_instantiated_and_renders()
    {
        $form = $this->makeFormWithDateWidget();
        $fields = $form->getFields();
        $this->assertArrayHasKey('field', $fields);
        $this->assertInstanceOf(DateWidget::class, $fields['field']['widget']);
        $output = $form->renderField('field');
        $this->assertIsString($output);
    }
}
