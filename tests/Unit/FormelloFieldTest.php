<?php

namespace Tests\Unit;

use Metalogico\Formello\FormelloField;
use Orchestra\Testbench\TestCase;

class FormelloFieldTest extends TestCase
{
    public function test_make_creates_field_with_name()
    {
        $field = FormelloField::make('email');

        $this->assertEquals('email', $field->getName());
    }

    public function test_label_is_set()
    {
        $field = FormelloField::make('name')->label('Full Name');

        $this->assertEquals('Full Name', $field->getLabel());
        $this->assertEquals('Full Name', $field->toArray()['label']);
    }

    public function test_columns_is_set()
    {
        $field = FormelloField::make('name')->columns(6);

        $this->assertEquals(6, $field->getColumns());
        $this->assertEquals(6, $field->toArray()['columns']);
    }

    public function test_widget_type_and_options()
    {
        $field = FormelloField::make('category_id')
            ->widget('tomselect', ['placeholder' => 'Pick one']);

        $this->assertEquals('tomselect', $field->getWidgetType());
        $this->assertEquals(['placeholder' => 'Pick one'], $field->getWidgetOptions());

        $array = $field->toArray();
        $this->assertEquals('tomselect', $array['widget']);
        $this->assertEquals(['placeholder' => 'Pick one'], $array['tomselect']);
    }

    public function test_widget_options_stored_under_widget_type_key()
    {
        $field = FormelloField::make('content')
            ->widget('wysiwyg', ['height' => 300]);

        $array = $field->toArray();
        $this->assertArrayHasKey('wysiwyg', $array);
        $this->assertEquals(['height' => 300], $array['wysiwyg']);
    }

    public function test_value_is_set_and_tracked()
    {
        $field = FormelloField::make('status')->value('active');

        $this->assertTrue($field->hasValueSet());
        $this->assertEquals('active', $field->toArray()['value']);
    }

    public function test_value_not_set_by_default()
    {
        $field = FormelloField::make('status');

        $this->assertFalse($field->hasValueSet());
        $this->assertArrayNotHasKey('value', $field->toArray());
    }

    public function test_null_value_is_tracked()
    {
        $field = FormelloField::make('status')->value(null);

        $this->assertTrue($field->hasValueSet());
        $this->assertArrayHasKey('value', $field->toArray());
        $this->assertNull($field->toArray()['value']);
    }

    public function test_choices()
    {
        $choices = ['a' => 'Alpha', 'b' => 'Beta'];
        $field = FormelloField::make('tags')->choices($choices);

        $this->assertEquals($choices, $field->toArray()['choices']);
    }

    public function test_choices_with_closure()
    {
        $field = FormelloField::make('tags')->choices(fn () => ['x' => 'X']);

        $array = $field->toArray();
        $this->assertArrayHasKey('choices', $array);
        $this->assertIsCallable($array['choices']);
    }

    public function test_multiple()
    {
        $field = FormelloField::make('tags')->multiple();

        $this->assertTrue($field->toArray()['multiple']);
    }

    public function test_multiple_false_not_in_array()
    {
        $field = FormelloField::make('tags')->multiple(false);

        $this->assertArrayNotHasKey('multiple', $field->toArray());
    }

    public function test_attributes()
    {
        $field = FormelloField::make('name')
            ->attributes(['required' => true, 'maxlength' => 255]);

        $this->assertEquals(
            ['required' => true, 'maxlength' => 255],
            $field->toArray()['attributes']
        );
    }

    public function test_required_shortcut()
    {
        $field = FormelloField::make('name')->required();

        $this->assertTrue($field->toArray()['attributes']['required']);
    }

    public function test_readonly_shortcut()
    {
        $field = FormelloField::make('name')->readonly();

        $this->assertTrue($field->toArray()['attributes']['readonly']);
    }

    public function test_disabled_shortcut()
    {
        $field = FormelloField::make('name')->disabled();

        $this->assertTrue($field->toArray()['attributes']['disabled']);
    }

    public function test_help()
    {
        $field = FormelloField::make('name')->help('Enter your name');

        $this->assertEquals('Enter your name', $field->toArray()['help']);
    }

    public function test_icon()
    {
        $field = FormelloField::make('price')->icon('fas fa-euro');

        $this->assertEquals('fas fa-euro', $field->toArray()['icon']);
    }

    public function test_type()
    {
        $field = FormelloField::make('email')->type('email');

        $this->assertEquals('email', $field->toArray()['type']);
    }

    public function test_format()
    {
        $field = FormelloField::make('date')->format('d/m/Y');

        $this->assertEquals('d/m/Y', $field->toArray()['format']);
    }

    public function test_reactive()
    {
        $config = ['client' => 'filterProvinces', 'dependsOn' => ['region_id']];
        $field = FormelloField::make('province_id')->reactive($config);

        $this->assertEquals($config, $field->toArray()['reactive']);
    }

    public function test_extra()
    {
        $field = FormelloField::make('price')
            ->extra('mask', ['mask' => 'Number'])
            ->extra('prefix', '$');

        $array = $field->toArray();
        $this->assertEquals(['mask' => 'Number'], $array['mask']);
        $this->assertEquals('$', $array['prefix']);
    }

    public function test_fluent_chaining()
    {
        $field = FormelloField::make('province_id')
            ->label('Provincia')
            ->columns(9)
            ->widget('tomselect', [
                'placeholder' => 'Seleziona una provincia',
            ])
            ->choices(['1' => 'Roma', '2' => 'Milano'])
            ->required()
            ->help('Select a province');

        $array = $field->toArray();

        $this->assertEquals('province_id', $field->getName());
        $this->assertEquals('Provincia', $array['label']);
        $this->assertEquals(9, $array['columns']);
        $this->assertEquals('tomselect', $array['widget']);
        $this->assertEquals(['placeholder' => 'Seleziona una provincia'], $array['tomselect']);
        $this->assertEquals(['1' => 'Roma', '2' => 'Milano'], $array['choices']);
        $this->assertTrue($array['attributes']['required']);
        $this->assertEquals('Select a province', $array['help']);
    }

    public function test_to_array_omits_unset_keys()
    {
        $field = FormelloField::make('name')->label('Name');

        $array = $field->toArray();

        $this->assertArrayHasKey('label', $array);
        $this->assertArrayNotHasKey('widget', $array);
        $this->assertArrayNotHasKey('columns', $array);
        $this->assertArrayNotHasKey('value', $array);
        $this->assertArrayNotHasKey('choices', $array);
        $this->assertArrayNotHasKey('multiple', $array);
        $this->assertArrayNotHasKey('attributes', $array);
        $this->assertArrayNotHasKey('help', $array);
        $this->assertArrayNotHasKey('icon', $array);
        $this->assertArrayNotHasKey('type', $array);
        $this->assertArrayNotHasKey('format', $array);
        $this->assertArrayNotHasKey('reactive', $array);
    }

    public function test_widget_options_not_set_when_empty()
    {
        $field = FormelloField::make('name')->widget('text');

        $array = $field->toArray();
        $this->assertEquals('text', $array['widget']);
        $this->assertArrayNotHasKey('text', $array);
    }
}
