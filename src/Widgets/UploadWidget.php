<?php

namespace Metalogico\Formello\Widgets;

class UploadWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'upload';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig['attributes'] = $fieldConfig['attributes'] ?? [];
        $fieldConfig['attributes']['class'] = trim(($fieldConfig['attributes']['class'] ?? '') . ' form-control');
        $fieldConfig['attributes']['id'] = $fieldConfig['attributes']['id'] ?? $name;
        $fieldConfig['attributes']['type'] = $fieldConfig['type'] ?? 'file';

        return [
            'name' => $name,
            'value' => $value,
            'label' => $fieldConfig['label'] ?? null,
            'config' => $fieldConfig,
            'errors' => $errors,
        ];
    }
}
