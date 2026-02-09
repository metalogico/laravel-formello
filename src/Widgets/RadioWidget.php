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
        $fieldConfig['attributes']['class'] = trim(($fieldConfig['attributes']['class'] ?? ''));

        return [
            'name' => $name,
            'value' => old($name, $value),
            'label' => $fieldConfig['label'] ?? null,
            'config' => $fieldConfig,
            'errors' => $errors,
            'choices' => $this->resolveChoices($fieldConfig),
        ];
    }

    protected function resolveChoices(array $fieldConfig): array
    {
        // Accept 'choices' (preferred) or 'options' (backward compat)
        $choices = $fieldConfig['choices'] ?? ($fieldConfig['options'] ?? []);

        if (is_callable($choices)) {
            $choices = call_user_func($choices);
        }

        return $choices;
    }
}
