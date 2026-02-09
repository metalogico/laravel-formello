<?php

namespace Tests\Unit;

use Metalogico\Formello\Support\FormelloState;
use Orchestra\Testbench\TestCase;

class FormelloStateTest extends TestCase
{
    public function test_get_returns_initial_value()
    {
        $state = new FormelloState(['name' => 'John', 'age' => 30]);

        $this->assertEquals('John', $state->get('name'));
        $this->assertEquals(30, $state->get('age'));
    }

    public function test_get_returns_null_for_missing_field()
    {
        $state = new FormelloState(['name' => 'John']);

        $this->assertNull($state->get('missing'));
    }

    public function test_set_updates_value_and_tracks_change()
    {
        $state = new FormelloState(['name' => 'John']);

        $state->set('name', 'Jane');

        $this->assertEquals('Jane', $state->get('name'));
        $changes = $state->getChanges();
        $this->assertEquals(['name' => 'Jane'], $changes['values']);
    }

    public function test_set_new_field()
    {
        $state = new FormelloState([]);

        $state->set('total', 100);

        $this->assertEquals(100, $state->get('total'));
        $this->assertEquals(['total' => 100], $state->getChanges()['values']);
    }

    public function test_set_null_value()
    {
        $state = new FormelloState(['province_id' => 5]);

        $state->set('province_id', null);

        $this->assertNull($state->get('province_id'));
        $this->assertArrayHasKey('province_id', $state->getChanges()['values']);
    }

    public function test_set_options()
    {
        $state = new FormelloState([]);
        $options = ['1' => 'Torino', '2' => 'Cuneo', '3' => 'Asti'];

        $state->setOptions('province_id', $options);

        $changes = $state->getChanges();
        $this->assertEquals($options, $changes['options']['province_id']);
    }

    public function test_set_attributes()
    {
        $state = new FormelloState([]);

        $state->setAttributes('status_other', ['hidden' => true, 'required' => false]);

        $changes = $state->getChanges();
        $this->assertEquals(
            ['hidden' => true, 'required' => false],
            $changes['attributes']['status_other']
        );
    }

    public function test_set_attributes_merges_multiple_calls()
    {
        $state = new FormelloState([]);

        $state->setAttributes('field', ['hidden' => true]);
        $state->setAttributes('field', ['disabled' => true]);

        $changes = $state->getChanges();
        $this->assertEquals(
            ['hidden' => true, 'disabled' => true],
            $changes['attributes']['field']
        );
    }

    public function test_set_attributes_overwrites_same_key()
    {
        $state = new FormelloState([]);

        $state->setAttributes('field', ['hidden' => true]);
        $state->setAttributes('field', ['hidden' => false]);

        $changes = $state->getChanges();
        $this->assertFalse($changes['attributes']['field']['hidden']);
    }

    public function test_get_changes_returns_empty_when_no_changes()
    {
        $state = new FormelloState(['name' => 'John']);

        $this->assertEmpty($state->getChanges());
    }

    public function test_get_changes_filters_empty_sections()
    {
        $state = new FormelloState([]);

        $state->set('total', 100);

        $changes = $state->getChanges();
        $this->assertArrayHasKey('values', $changes);
        $this->assertArrayNotHasKey('options', $changes);
        $this->assertArrayNotHasKey('attributes', $changes);
    }

    public function test_combined_changes()
    {
        $state = new FormelloState(['region_id' => 1]);

        $state->set('province_id', null);
        $state->setOptions('province_id', ['1' => 'Torino', '2' => 'Cuneo']);
        $state->setAttributes('province_id', ['disabled' => false]);

        $changes = $state->getChanges();
        $this->assertArrayHasKey('values', $changes);
        $this->assertArrayHasKey('options', $changes);
        $this->assertArrayHasKey('attributes', $changes);
    }

    public function test_multiple_fields_tracked()
    {
        $state = new FormelloState(['a' => 1, 'b' => 2]);

        $state->set('a', 10);
        $state->set('b', 20);
        $state->set('c', 30);

        $changes = $state->getChanges();
        $this->assertEquals(['a' => 10, 'b' => 20, 'c' => 30], $changes['values']);
    }
}
