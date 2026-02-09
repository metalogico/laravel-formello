<?php

namespace Tests\Unit;

use Orchestra\Testbench\TestCase;
use Metalogico\Formello\Formello;
use Metalogico\Formello\FormelloField;
use Metalogico\Formello\Widgets\MaskWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ViewErrorBag;

class MaskWidgetTest extends TestCase
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

    public function test_mask_widget_is_instantiated_and_renders()
    {
        $form = new class($this->makeDummyModel(), new ViewErrorBag()) extends Formello {
            protected function fields(): array {
                return [
                    FormelloField::make('phone')
                        ->widget('mask')
                        ->extra('mask', '+{39} 000 000 0000'),
                ];
            }
            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };

        $fields = $form->getFields();
        $this->assertArrayHasKey('phone', $fields);
        $this->assertInstanceOf(MaskWidget::class, $fields['phone']['widget']);

        $output = $form->renderField('phone');
        $this->assertIsString($output);
        $this->assertStringContainsString('data-formello-mask', $output);
    }

    public function test_mask_widget_view_data_includes_mask_attribute()
    {
        $widget = new MaskWidget();
        $view_data = $widget->getViewData('phone', '', [
            'mask' => ['mask' => '000-000-0000'],
        ]);

        $this->assertArrayHasKey('config', $view_data);
        $this->assertArrayHasKey('data-formello-mask', $view_data['config']['attributes']);

        $mask_config = json_decode($view_data['config']['attributes']['data-formello-mask'], true);
        $this->assertEquals('000-000-0000', $mask_config['mask']);
    }

    public function test_mask_widget_without_mask_config()
    {
        $widget = new MaskWidget();
        $view_data = $widget->getViewData('field', '', []);

        $this->assertArrayHasKey('config', $view_data);
        // No mask attribute should be set when mask config is not provided
        $this->assertArrayNotHasKey('data-formello-mask', $view_data['config']['attributes']);
    }

    public function test_mask_widget_returns_imask_assets()
    {
        $widget = new MaskWidget();
        $assets = $widget->getAssets();

        $this->assertNotNull($assets);
        $this->assertContains('imask.min.js', $assets['scripts']);
    }
}
