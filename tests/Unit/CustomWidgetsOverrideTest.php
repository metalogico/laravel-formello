<?php

namespace Tests\Unit;

use Orchestra\Testbench\TestCase;
use Metalogico\Formello\Formello;
use Metalogico\Formello\FormelloField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ViewErrorBag;
use Metalogico\Formello\Widgets\TextWidget;
use Metalogico\Formello\Interfaces\WidgetInterface;
use Metalogico\Formello\Widgets\BaseWidget;

class CustomWidgetsOverrideTest extends TestCase
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

    public function test_custom_widgets_override_built_in_alias()
    {
        // Register a custom widget that overrides the 'text' alias
        config()->set('formello.custom_widgets', [
            'text' => \Tests\Unit\TestTextWidget::class,
        ]);

        // Build a simple form that uses the 'text' alias
        $form = new class($this->makeDummyModel(), new ViewErrorBag()) extends Formello {
            protected function fields(): array {
                return [
                    FormelloField::make('field')->widget('text'),
                ];
            }
            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };

        $fields = $form->getFields();
        $this->assertArrayHasKey('field', $fields);
        $this->assertInstanceOf(TestTextWidget::class, $fields['field']['widget']);
        $output = $form->renderField('field');
        $this->assertIsString($output);
        $this->assertStringContainsString('data-test-text-widget', $output);
    }
}

// Simple test widget that replaces the built-in TextWidget
class TestTextWidget extends BaseWidget implements WidgetInterface
{
    public function getWidgetName(): string
    {
        return 'text';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        return [
            'name' => $name,
            'value' => $value,
            'label' => $fieldConfig['label'] ?? null,
            'config' => $fieldConfig,
            'errors' => $errors,
        ];
    }

    public function render($name, $value, array $fieldConfig, $errors = null): string
    {
        // Render a minimal recognizable markup for assertion
        return '<div data-test-text-widget>overridden</div>';
    }
}
