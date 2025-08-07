<?php

namespace Tests\Unit;

use Metalogico\Formello\Formello;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\ViewErrorBag;
use Metalogico\Formello\FormelloField;
use Illuminate\Database\Eloquent\Model;
use Metalogico\Formello\Widgets\WysiwygWidget;

class WysiwygWidgetTest extends TestCase
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
        return new class extends Model
        {
            public function getTable()
            {
                return 'dummy';
            }
        };
    }

    private function makeFormWithWysiwygWidget()
    {
        return new class($this->makeDummyModel(), new ViewErrorBag) extends Formello
        {
            protected function fields(): array
            {
                return [
                    FormelloField::make('content')
                        ->widget(WysiwygWidget::class, [
                            'jodit' => [
                                'toolbar' => ['bold', 'italic', 'link'],
                                'language' => 'it',
                            ],
                        ]),
                ];
            }

            protected function create(): array
            {
                return [];
            }

            protected function edit(): array
            {
                return [];
            }
        };
    }

    public function test_wysiwyg_widget_is_instantiated_and_renders()
    {
        $form = $this->makeFormWithWysiwygWidget();
        $fields = $form->getFields();

        $this->assertArrayHasKey('content', $fields);
        $this->assertInstanceOf(WysiwygWidget::class, $fields['content']['widget']);

        $output = $form->renderField('content');
        $this->assertIsString($output);
        $this->assertStringContainsString('data-formello-wysiwyg', $output);
    }

    public function test_wysiwyg_widget_includes_configuration()
    {
        $field = FormelloField::make('content')
            ->widget(WysiwygWidget::class, [
                'jodit' => [
                    'toolbar' => ['bold', 'italic'],
                    'language' => 'en',
                ],
            ]);
        $viewData = $field->getWidget()->getViewData($field, 'test value');

        $this->assertArrayHasKey('config', $viewData);
        $this->assertArrayHasKey('attributes', $viewData['config']);
        $this->assertArrayHasKey('data-formello-wysiwyg', $viewData['config']['attributes']);

        $fieldConfig = json_decode($viewData['config']['attributes']['data-formello-wysiwyg'], true);
        $this->assertEquals(['bold', 'italic'], $fieldConfig['toolbar']);
        $this->assertEquals('en', $fieldConfig['language']);
    }

    public function test_wysiwyg_widget_with_empty_configuration()
    {
        $field = FormelloField::make('content')
            ->widget(WysiwygWidget::class);
        $viewData = $field->getWidget()->getViewData($field, 'test value');

        $this->assertArrayHasKey('config', $viewData);
        $this->assertArrayHasKey('attributes', $viewData['config']);
        $this->assertArrayHasKey('data-formello-wysiwyg', $viewData['config']['attributes']);

        $fieldConfig = json_decode($viewData['config']['attributes']['data-formello-wysiwyg'], true);
        $this->assertEquals([], $fieldConfig);
    }
}
