<?php

namespace Metalogico\Formello\Widgets;

class Select2Widget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'select2';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig = $this->mergeDefaultAttributes($fieldConfig, [
            'class' => 'form-control select2',
        ], $name);
        
        // Handle multiple selection
        if (!empty($fieldConfig['multiple'])) {
            $fieldConfig['attributes']['multiple'] = 'multiple';
            $name .= '[]';
        }

        return [
            'name' => $name,
            'value' => old($name, $value),
            'label' => $fieldConfig['label'] ?? null,
            'config' => $fieldConfig,
            'errors' => $errors,
            'choices' => $this->resolveChoices($fieldConfig['choices'] ?? []),
            'ajax' => !empty($fieldConfig['ajax']),
        ];
    }

    protected function resolveChoices($choices): array
    {
        if (is_callable($choices)) {
            return call_user_func($choices);
        }
        
        return $choices;
    }
}