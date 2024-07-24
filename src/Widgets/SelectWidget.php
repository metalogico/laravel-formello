<?php

namespace Metalogico\Formello\Widgets;

use Illuminate\Support\Arr;

class SelectWidget extends BaseWidget
{
    public function getTemplate(): string
    {
        return 'formello::widgets.bootstrap5.select';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig['attributes'] = $fieldConfig['attributes'] ?? [];
        $fieldConfig['attributes']['class'] = trim(($attributes['class'] ?? '') . ' form-control');
        $fieldConfig['attributes']['id'] = $attributes['id'] ?? $name;

        $choices = $this->resolveChoices($fieldConfig['choices']);

        return [
            'name' => $name,
            'value' => old($name, $value),
            'label' => $fieldConfig['label'] ?? null,
            'config' => $fieldConfig,
            'errors' => $errors,
            'choices' => $choices,
        ];
    }

    protected function resolveChoices($choices)
    {
        if (is_callable($choices)) {
            return call_user_func($choices);
        }

        return $choices;
    }
}
