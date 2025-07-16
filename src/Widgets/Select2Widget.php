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
        // Imposta i valori di default
        $defaults = [
            'class' => 'form-control select2',
            'multiple' => false,
        ];

        // Unisci con le configurazioni fornite
        $fieldConfig = array_merge($defaults, $fieldConfig);
        $fieldConfig = $this->mergeDefaultAttributes($fieldConfig, $defaults, $name);

        // Handle multiple selection
        if (! empty($fieldConfig['multiple'])) {
            $name .= '[]';
        }

        // Determina se usare AJAX in base alla presenza di una route
        $usesAjax = ! empty($fieldConfig['route']);

        // Se non stiamo usando AJAX, risolviamo le choices
        $choices = $usesAjax ? [] : $this->resolveChoices($fieldConfig['choices'] ?? []);

        return [
            'name' => $name,
            'value' => old($name, $value),
            'label' => $fieldConfig['label'] ?? null,
            'config' => $fieldConfig,
            'errors' => $errors,
            'choices' => $choices,
            'usesAjax' => $usesAjax,
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
