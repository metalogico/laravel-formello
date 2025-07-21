<?php

namespace Metalogico\Formello\Widgets;

use Illuminate\Support\Arr;

class CheckboxesWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'checkboxes';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig['attributes'] = $fieldConfig['attributes'] ?? [];
        $fieldConfig['attributes']['id'] = $fieldConfig['attributes']['id'] ?? $name;

        $choices = $this->resolveChoices($fieldConfig['choices']) ?? [];

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
