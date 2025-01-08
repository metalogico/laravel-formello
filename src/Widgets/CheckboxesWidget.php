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
        $fieldConfig['options'] = $fieldConfig['options'] ?? [];

        $selectedValues = is_array($value) ? $value : [];

        $checkboxes = array_map(function ($option) use ($selectedValues, $name) {
            return [
                'label' => $option['label'] ?? $option,
                'value' => $option['value'] ?? $option,
                'checked' => in_array($option['value'] ?? $option, $selectedValues),
                'name' => $name . '[]',
            ];
        }, $fieldConfig['options']);

        return [
            'checkboxes' => $checkboxes,
            'attributes' => $fieldConfig['attributes'],
            'errors' => $errors,
        ];
    }

    public function getValidationRules(array $fieldConfig): array
    {
        return $fieldConfig['validation'] ?? [];
    }
}