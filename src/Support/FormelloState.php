<?php

namespace Metalogico\Formello\Support;

class FormelloState
{
    private array $data;

    private array $value_changes = [];

    private array $options_changes = [];

    private array $attributes_changes = [];

    public function __construct(array $initial_data = [])
    {
        $this->data = $initial_data;
    }

    public function get(string $field): mixed
    {
        return $this->data[$field] ?? null;
    }

    public function set(string $field, mixed $value): void
    {
        $this->data[$field] = $value;
        $this->value_changes[$field] = $value;
    }

    public function setOptions(string $field, array $options): void
    {
        $this->options_changes[$field] = $options;
    }

    public function setAttributes(string $field, array $attributes): void
    {
        $this->attributes_changes[$field] = array_merge(
            $this->attributes_changes[$field] ?? [],
            $attributes
        );
    }

    public function getChanges(): array
    {
        return array_filter([
            'values' => $this->value_changes,
            'options' => $this->options_changes,
            'attributes' => $this->attributes_changes,
        ]);
    }
}
