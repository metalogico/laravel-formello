<?php

namespace Metalogico\Formello\Widgets;

class RangeWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'range';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig['attributes'] = $fieldConfig['attributes'] ?? [];
        $fieldConfig['attributes']['class'] = trim(($fieldConfig['attributes']['class'] ?? '') . ' form-range');
        $fieldConfig['attributes']['type'] = 'range';
        $fieldConfig['attributes']['id'] = $fieldConfig['attributes']['id'] ?? $name;

        // Set default min, max, and step if not provided
        $fieldConfig['attributes']['min'] = $fieldConfig['attributes']['min'] ?? 0;
        $fieldConfig['attributes']['max'] = $fieldConfig['attributes']['max'] ?? 100;
        $fieldConfig['attributes']['step'] = $fieldConfig['attributes']['step'] ?? 1;

        return [
            'name' => $name,
            'value' => old($name, $value),
            'label' => $fieldConfig['label'] ?? null,
            'config' => $fieldConfig,
            'errors' => $errors,
            'showValue' => $fieldConfig['showValue'] ?? true,
        ];
    }
}