<?php

namespace Metalogico\Formello\Widgets;

class TextWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'text';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig['attributes'] = $fieldConfig['attributes'] ?? [];
        $fieldConfig['attributes']['class'] = trim(($fieldConfig['attributes']['class'] ?? '').' form-control');
        $fieldConfig['attributes']['id'] = $fieldConfig['attributes']['id'] ?? $name;
        $fieldConfig['attributes']['type'] = $fieldConfig['type'] ?? 'text';

        $typeAttributes = match ($fieldConfig['attributes']['type']) {
            'number' => ['inputmode' => 'numeric', 'pattern' => '[0-9]*'],
            'email' => ['autocomplete' => 'email'],
            'password' => ['autocomplete' => 'new-password'],
            default => []
        };

        $fieldConfig['attributes'] = array_merge($fieldConfig['attributes'], $typeAttributes);

        $safeValue = $value;
        if ($fieldConfig['attributes']['type'] === 'password') {
            $safeValue = '';
        }

        return [
            'name' => $name,
            'value' => old($name, $safeValue),
            'label' => $fieldConfig['label'] ?? null,
            'config' => $fieldConfig,
            'errors' => $errors,
        ];
    }

    /**
     * Get assets for TextWidget - no assets needed
     */
    public function getAssets(?array $fieldConfig = null): ?array
    {
        return null;
    }
}
