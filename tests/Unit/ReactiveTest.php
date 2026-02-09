<?php

namespace Tests\Unit;

use Metalogico\Formello\Formello;
use Metalogico\Formello\FormelloField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ViewErrorBag;
use Orchestra\Testbench\TestCase;

class ReactiveTest extends TestCase
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

    private function makeFormWithReactiveFields()
    {
        return new class($this->makeDummyModel(), new ViewErrorBag) extends Formello {
            protected function fields(): array
            {
                return [
                    FormelloField::make('total')
                        ->label('Amount')
                        ->widget('mask')
                        ->reactive([
                            'client' => 'calculateYearlyTotal',
                        ]),
                    FormelloField::make('contract_duration')
                        ->label('Duration')
                        ->type('number')
                        ->reactive([
                            'client' => 'calculateYearlyTotal',
                        ]),
                    FormelloField::make('total_yearly')
                        ->label('Yearly Amount')
                        ->readonly(),
                    FormelloField::make('region_id')
                        ->widget('tomselect')
                        ->reactive([
                            'server' => 'onRegionChanged',
                        ]),
                    FormelloField::make('province_id')
                        ->widget('tomselect'),
                    FormelloField::make('complex')
                        ->reactive([
                            'client' => 'quickEstimate',
                            'server' => 'preciseCalculation',
                        ]),
                    FormelloField::make('quantity')
                        ->reactive([
                            'client' => ['updateSubtotal', 'updateShipping'],
                        ]),
                ];
            }

            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };
    }

    private function makeFormWithoutReactiveFields()
    {
        return new class($this->makeDummyModel(), new ViewErrorBag) extends Formello {
            protected function fields(): array
            {
                return [
                    FormelloField::make('name')->label('Name')->widget('text'),
                    FormelloField::make('email')->label('Email')->widget('text'),
                ];
            }

            protected function create(): array { return []; }
            protected function edit(): array { return []; }
        };
    }

    public function test_has_reactive_fields_returns_true()
    {
        $form = $this->makeFormWithReactiveFields();

        $this->assertTrue($form->hasReactiveFields());
    }

    public function test_has_reactive_fields_returns_false()
    {
        $form = $this->makeFormWithoutReactiveFields();

        $this->assertFalse($form->hasReactiveFields());
    }

    public function test_get_reactive_map_returns_only_reactive_fields()
    {
        $form = $this->makeFormWithReactiveFields();
        $map = $form->getReactiveMap();

        // Should include reactive fields only
        $this->assertArrayHasKey('total', $map);
        $this->assertArrayHasKey('contract_duration', $map);
        $this->assertArrayHasKey('region_id', $map);
        $this->assertArrayHasKey('complex', $map);
        $this->assertArrayHasKey('quantity', $map);

        // Should NOT include non-reactive fields
        $this->assertArrayNotHasKey('total_yearly', $map);
        $this->assertArrayNotHasKey('province_id', $map);
    }

    public function test_get_reactive_map_client_callbacks()
    {
        $form = $this->makeFormWithReactiveFields();
        $map = $form->getReactiveMap();

        $this->assertEquals('calculateYearlyTotal', $map['total']['client']);
        $this->assertNull($map['total']['server']);
    }

    public function test_get_reactive_map_server_callbacks()
    {
        $form = $this->makeFormWithReactiveFields();
        $map = $form->getReactiveMap();

        $this->assertEquals('onRegionChanged', $map['region_id']['server']);
        $this->assertNull($map['region_id']['client']);
    }

    public function test_get_reactive_map_both_layers()
    {
        $form = $this->makeFormWithReactiveFields();
        $map = $form->getReactiveMap();

        $this->assertEquals('quickEstimate', $map['complex']['client']);
        $this->assertEquals('preciseCalculation', $map['complex']['server']);
    }

    public function test_get_reactive_map_multiple_callbacks()
    {
        $form = $this->makeFormWithReactiveFields();
        $map = $form->getReactiveMap();

        $this->assertEquals(['updateSubtotal', 'updateShipping'], $map['quantity']['client']);
    }

    public function test_get_reactive_map_empty_for_non_reactive_form()
    {
        $form = $this->makeFormWithoutReactiveFields();
        $map = $form->getReactiveMap();

        $this->assertEmpty($map);
    }

    public function test_get_field_config_returns_config()
    {
        $form = $this->makeFormWithReactiveFields();
        $config = $form->getFieldConfig('total');

        $this->assertEquals('Amount', $config['label']);
        $this->assertEquals('mask', $config['widget']);
        $this->assertArrayHasKey('reactive', $config);
        $this->assertEquals('calculateYearlyTotal', $config['reactive']['client']);
    }

    public function test_get_field_config_throws_for_missing_field()
    {
        $form = $this->makeFormWithReactiveFields();

        $this->expectException(\InvalidArgumentException::class);
        $form->getFieldConfig('nonexistent');
    }
}
