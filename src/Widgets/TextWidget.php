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
        $fieldConfig = $this->normalizeAttributes($fieldConfig, $name);
        $fieldConfig['attributes']['type'] = $fieldConfig['type'] ?? ($fieldConfig['attributes']['type'] ?? 'text');

        $typeAttributes = match ($fieldConfig['attributes']['type']) {
            'number' => ['inputmode' => 'numeric', 'pattern' => '[0-9]*'],
            'email' => ['autocomplete' => 'email'],
            'password' => ['autocomplete' => 'new-password'],
            default => []
        };

        $fieldConfig['attributes'] = array_merge($fieldConfig['attributes'], $typeAttributes);

        if ($fieldConfig['attributes']['type'] === 'password') {
            $value = '';
        }

        return $this->viewPayload($name, $value, $fieldConfig, $errors);
    }
}
