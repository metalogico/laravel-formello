<?php

namespace Tests\Unit;

use Orchestra\Testbench\TestCase;
use Metalogico\Formello\Formello;
use Metalogico\Formello\FormelloField;
use Metalogico\Formello\Widgets\TomSelectWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ViewErrorBag;

class TomSelectWidgetTest extends TestCase
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

    public function test_tomselect_widget_is_instantiated_and_renders()
    {
        $form = new class($this->makeDummyModel(), new ViewErrorBag()) extends Formello {
            protected function fields(): array {
                return [
                    FormelloField::make('category_id')
                        ->widget('tomselect')
                        ->choices(['1' => 'Cat A', '2' => 'Cat B']),
                ];
            }
            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };

        $fields = $form->getFields();
        $this->assertArrayHasKey('category_id', $fields);
        $this->assertInstanceOf(TomSelectWidget::class, $fields['category_id']['widget']);

        $output = $form->renderField('category_id');
        $this->assertIsString($output);
        $this->assertStringContainsString('data-formello-tomselect', $output);
        $this->assertStringContainsString('Cat A', $output);
        $this->assertStringContainsString('Cat B', $output);
    }

    public function test_tomselect_widget_renders_multiple()
    {
        $form = new class($this->makeDummyModel(), new ViewErrorBag()) extends Formello {
            protected function fields(): array {
                return [
                    FormelloField::make('tags')
                        ->widget('tomselect')
                        ->multiple()
                        ->choices(['a' => 'Alpha', 'b' => 'Beta']),
                ];
            }
            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };

        $output = $form->renderField('tags');
        $this->assertStringContainsString('multiple', $output);
        $this->assertStringContainsString('name="tags[]"', $output);
    }

    public function test_tomselect_widget_view_data_contains_expected_keys()
    {
        $widget = new TomSelectWidget();
        $view_data = $widget->getViewData('field', null, [
            'choices' => ['x' => 'X'],
        ]);

        $this->assertArrayHasKey('name', $view_data);
        $this->assertArrayHasKey('value', $view_data);
        $this->assertArrayHasKey('label', $view_data);
        $this->assertArrayHasKey('config', $view_data);
        $this->assertArrayHasKey('errors', $view_data);
        $this->assertArrayHasKey('choices', $view_data);
        $this->assertArrayHasKey('usesAjax', $view_data);
        $this->assertFalse($view_data['usesAjax']);
        $this->assertEquals(['x' => 'X'], $view_data['choices']);
    }

    public function test_tomselect_widget_ajax_config()
    {
        $widget = new TomSelectWidget();
        $view_data = $widget->getViewData('field', null, [
            'tomselect' => [
                'route' => '/api/search',
                'model' => 'App\\Models\\Category',
                'label_field' => 'name',
                'value_field' => 'id',
            ],
        ]);

        $this->assertTrue($view_data['usesAjax']);
        $ts_config = json_decode($view_data['config']['attributes']['data-formello-tomselect'], true);
        $this->assertArrayHasKey('ajax', $ts_config);
        $this->assertEquals('/api/search', $ts_config['ajax']['url']);
        $this->assertSame(0, $ts_config['ajax']['minLength']);
        $this->assertSame('focus', $ts_config['preload']);
        $this->assertSame('body', $ts_config['dropdownParent']);
    }

    public function test_tomselect_widget_returns_assets()
    {
        $widget = new TomSelectWidget();
        $assets = $widget->getAssets();

        $this->assertNotNull($assets);
        $this->assertArrayHasKey('scripts', $assets);
        $this->assertArrayHasKey('styles', $assets);
        $this->assertContains('tom-select.complete.js', $assets['scripts']);
    }
}
