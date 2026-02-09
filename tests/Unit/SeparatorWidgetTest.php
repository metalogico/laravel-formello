<?php

namespace Tests\Unit;

use Orchestra\Testbench\TestCase;
use Metalogico\Formello\Formello;
use Metalogico\Formello\FormelloField;
use Metalogico\Formello\Widgets\SeparatorWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ViewErrorBag;

class SeparatorWidgetTest extends TestCase
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

    private function makeFormWithSeparator(?string $label)
    {
        return new class($this->makeDummyModel(), new ViewErrorBag(), $label) extends Formello {
            public function __construct($model, $errors, private ?string $sepLabel)
            {
                parent::__construct($model, $errors);
            }
            protected function fields(): array {
                $field = FormelloField::make('sep')->widget('separator');
                if ($this->sepLabel !== null) {
                    $field->label($this->sepLabel);
                }
                return [$field];
            }
            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };
    }

    public function test_separator_widget_renders_with_label()
    {
        $form = $this->makeFormWithSeparator('Section Title');
        $output = $form->renderField('sep');
        $this->assertIsString($output);
        $this->assertStringContainsString('Section Title', $output);
        $this->assertStringContainsString('<hr', $output);
    }

    public function test_separator_widget_renders_without_label()
    {
        $form = $this->makeFormWithSeparator(null);
        $output = $form->renderField('sep');
        $this->assertIsString($output);
        $this->assertStringNotContainsString('<label', $output);
        $this->assertStringContainsString('<hr', $output);
    }
}
