<?php

namespace Metalogico\Formello\Widgets;

class SeparatorWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'separator';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        // No input element here; just a visual separator.
        // Allow passing optional custom classes/attributes for the wrapper and hr.
        $fieldConfig['attributes'] = $fieldConfig['attributes'] ?? [];
        $fieldConfig['hr'] = $fieldConfig['hr'] ?? [];

        return [
            'name' => $name,
            'label' => $fieldConfig['label'] ?? null,
            'config' => $fieldConfig,
        ];
    }

    public function getAssets(?array $fieldConfig = null): ?array
    {
        return null; // No assets required
    }
}
