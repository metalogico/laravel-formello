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
        $fieldConfig['attributes']['class'] = trim(($attributes['class'] ?? '') . ' form-control');
        $fieldConfig['attributes']['id'] = $attributes['id'] ?? $name;
        $fieldConfig['attributes']['type'] = $fieldConfig['type'] ?? 'text';

        $fieldConfig['attributes'] = array_merge(
            $fieldConfig['attributes'],
            $this->getTypeSpecificAttributes($fieldConfig['attributes']['type'])
        );

        return [
            'name' => $name,
            'value' => old($name, $value),
            'label' => $fieldConfig['label'] ?? null,
            'config' => $fieldConfig,
            'errors' => $errors,
        ];
    }

    protected function getTypeSpecificAttributes($type)
    {
        switch ($type) {
            case 'number':
                return ['inputmode' => 'numeric', 'pattern' => '[0-9]*'];
            case 'email':
                return ['autocomplete' => 'email'];
            case 'password':
                return ['autocomplete' => 'new-password'];
            default:
                return [];
        }
    }
}
