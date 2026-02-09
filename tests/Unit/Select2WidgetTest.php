<?php

namespace Tests\Unit;

use Orchestra\Testbench\TestCase;
use Metalogico\Formello\Formello;
use Metalogico\Formello\FormelloField;
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
                    FormelloField::make('field')
                        ->widget('select2')
                        ->choices(['a' => 'A', 'b' => 'B']),
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

    public function test_select2_does_not_render_multiple_attribute_by_default()
    {
        $form = new class($this->makeDummyModel(), new ViewErrorBag()) extends Formello {
            protected function fields(): array {
                return [
                    FormelloField::make('field')
                        ->widget('select2')
                        ->choices(['a' => 'A', 'b' => 'B']),
                    // 'multiple' omitted
                ];
            }
            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };

        $output = $form->renderField('field');
        // Ensure the HTML boolean attribute 'multiple' is not present
        $this->assertStringNotContainsString(' multiple', $output);
    }

    public function test_select2_does_not_render_multiple_attribute_when_config_false()
    {
        $form = new class($this->makeDummyModel(), new ViewErrorBag()) extends Formello {
            protected function fields(): array {
                return [
                    FormelloField::make('field')
                        ->widget('select2')
                        ->choices(['a' => 'A', 'b' => 'B'])
                        ->multiple(false),
                ];
            }
            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };

        $output = $form->renderField('field');
        // Ensure the HTML boolean attribute 'multiple' is not present
        $this->assertStringNotContainsString(' multiple', $output);
    }

    public function test_select2_renders_deprecation_warning()
    {
        $form = new class($this->makeDummyModel(), new ViewErrorBag()) extends Formello {
            protected function fields(): array {
                return [
                    FormelloField::make('field')
                        ->widget('select2')
                        ->choices(['a' => 'A', 'b' => 'B'])
                        ->multiple(),
                ];
            }
            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };

        $output = $form->renderField('field');
        // Select2 is deprecated: template shows a warning and returns early
        $this->assertStringContainsString('Deprecated', $output);
        $this->assertStringContainsString('tomselect', $output);
    }
}
