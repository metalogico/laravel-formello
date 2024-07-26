<?php

namespace Metalogico\Formello\Widgets;

class RadioWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'radio';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig['attributes'] = $fieldConfig['attributes'] ?? [];
        $fieldConfig['attributes']['class'] = trim(($fieldConfig['attributes']['class'] ?? '') . ' form-check-input');

        return [
            'name' => $name,
            'value' => old($name, $value),
            'label' => $fieldConfig['label'] ?? null,
            'config' => $fieldConfig,
            'errors' => $errors,
            'options' => $this->getOptions($fieldConfig),
        ];
    }

    protected function getOptions(array $fieldConfig): array
    {
        $options = $fieldConfig['options'] ?? [];

        if (is_callable($options)) {
            $options = call_user_func($options);
        }

        return $options;
    }
}