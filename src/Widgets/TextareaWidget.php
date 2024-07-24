<?php

namespace Metalogico\Formello\Widgets;

class TextareaWidget extends BaseWidget
{
    public function getTemplate(): string
    {
        return 'formello::widgets.bootstrap5.textarea';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig['attributes'] = $fieldConfig['attributes'] ?? [];
        $fieldConfig['attributes']['class'] = trim(($attributes['class'] ?? '') . ' form-control');
        $fieldConfig['attributes']['id'] = $attributes['id'] ?? $name;
        $fieldConfig['attributes']['type'] = 'text';

        return [
            'name' => $name,
            'value' => old($name, $value),
            'label' => $fieldConfig['label'] ?? null,
            'config' => $fieldConfig,
            'errors' => $errors,
        ];
    }
}
