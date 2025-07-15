<?php

namespace Tests\Unit;

use Metalogico\Formello\Formello;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Database\Eloquent\Model;
use Metalogico\Formello\Widgets\TextWidget;

class FormelloTest extends TestCase
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

    public function test_formello_can_be_instantiated_and_renders()
    {
        $form = new class($this->makeDummyModel(), new ViewErrorBag()) extends Formello {
            protected function fields(): array {
                return [
                    'field' => [
                        'name' => 'test',
                        'label' => 'Test',
                        'widget' => new TextWidget(),
                    ],
                ];
            }
            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };
        $this->assertInstanceOf(Formello::class, $form);
        try {
            $output = $form->render();
            $this->assertIsString($output);
        } catch (\Throwable $e) {
            $this->fail('Render exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }
}
