<?php

namespace Metalogico\Formello\Widgets;

class ToggleWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'toggle';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig['attributes'] = $fieldConfig['attributes'] ?? [];
        $fieldConfig['attributes']['class'] = trim(($fieldConfig['attributes']['class'] ?? '') . ' form-check-input');
        $fieldConfig['attributes']['id'] = $fieldConfig['attributes']['id'] ?? $name;
        $fieldConfig['attributes']['type'] = 'checkbox';
        $fieldConfig['attributes']['role'] = 'switch';

        // Convert value to boolean
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        // Set checked attribute if the value is true
        if ($value) {
            $fieldConfig['attributes']['checked'] = 'checked';
        }

        return [
            'name' => $name,
            'value' => old($name, $value),
            'config' => $fieldConfig,
            'errors' => $errors,
        ];
    }
}
