<?php

namespace Tests\Unit;

use Orchestra\Testbench\TestCase;
use Metalogico\Formello\Formello;
use Metalogico\Formello\FormelloField;
use Metalogico\Formello\Widgets\UploadWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ViewErrorBag;

class UploadWidgetTest extends TestCase
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

    private function makeFormWithUploadWidget()
    {
        return new class($this->makeDummyModel(), new ViewErrorBag()) extends Formello {
            protected function fields(): array {
                return [
                    FormelloField::make('file')->widget('upload'),
                ];
            }
            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };
    }

    public function test_upload_widget_is_instantiated_and_renders_and_sets_enctype()
    {
        $form = $this->makeFormWithUploadWidget();
        $fields = $form->getFields();
        $this->assertArrayHasKey('file', $fields);
        $this->assertInstanceOf(UploadWidget::class, $fields['file']['widget']);
        $output = $form->renderField('file');
        $this->assertIsString($output);
        $formConfig = (new \ReflectionClass($form))->getProperty('formConfig');
        $formConfig->setAccessible(true);
        $config = $formConfig->getValue($form);
        $this->assertArrayHasKey('attributes', $config);
        $this->assertArrayHasKey('enctype', $config['attributes']);
        $this->assertEquals('multipart/form-data', $config['attributes']['enctype']);
    }
}
