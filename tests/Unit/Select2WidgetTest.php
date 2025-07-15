<?php

namespace Tests\Unit;

use Orchestra\Testbench\TestCase;
use Metalogico\Formello\Formello;
use Metalogico\Formello\Widgets\Select2Widget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ViewErrorBag;

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
                    'field' => [
                        'widget' => new Select2Widget(),
                        'choices' => ['a' => 'A', 'b' => 'B'],
                    ],
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
