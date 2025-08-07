<?php

namespace Tests\Unit;

use Metalogico\Formello\Formello;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\ViewErrorBag;
use Metalogico\Formello\FormelloField;
use Illuminate\Database\Eloquent\Model;
use Metalogico\Formello\Widgets\UploadWidget;

class FormelloMultipartTest extends TestCase
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
            public function getTable() {
                return 'dummy';
            }
        };
    }

    private function makeFormWithUpload()
    {
        return new class($this->makeDummyModel(), new ViewErrorBag()) extends Formello {
            protected function fields(): array {
                return [
                    FormelloField::make('file')
                        ->widget(UploadWidget::class)
                ];
            }
            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };
    }

    private function makeFormWithNoUpload()
    {
        return new class($this->makeDummyModel(), new ViewErrorBag()) extends Formello {
            protected function fields(): array {
                return [
                    FormelloField::make('name')
                        ->widget('TextWidget'),
                ];
            }
            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };
    }

    public function test_form_with_upload_has_multipart_enctype()
    {
        $form = $this->makeFormWithUpload();
        $formConfig = (new \ReflectionClass($form))->getProperty('formConfig');
        $formConfig->setAccessible(true);
        $config = $formConfig->getValue($form);
        $this->assertArrayHasKey('attributes', $config);
        $this->assertArrayHasKey('enctype', $config['attributes']);
        $this->assertEquals('multipart/form-data', $config['attributes']['enctype']);
    }

    public function test_form_without_upload_does_not_have_multipart_enctype()
    {
        $form = $this->makeFormWithNoUpload();
        $formConfig = (new \ReflectionClass($form))->getProperty('formConfig');
        $formConfig->setAccessible(true);
        $config = $formConfig->getValue($form);
        $this->assertTrue(!isset($config['attributes']) || !isset($config['attributes']['enctype']));
    }
}
