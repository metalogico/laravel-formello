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

        // Estrai la configurazione specifica di select2
        $select2Config = $fieldConfig['select2'] ?? [];
        $usesAjax = ! empty($select2Config['route']);

        $currentValue = old($name, $value);
        $choices = [];

        // Se usiamo AJAX e c'è un valore, dobbiamo caricare l'opzione iniziale
        if ($usesAjax && !empty($currentValue)) {
            $modelClass = $select2Config['model'] ?? null;
            $labelField = $select2Config['label_field'] ?? 'name';
            $valueField = $select2Config['value_field'] ?? 'id';

            if ($modelClass) {
                $initialItems = $modelClass::whereIn($valueField, (array) $currentValue)->get();
                foreach ($initialItems as $item) {
                    $choices[$item->$valueField] = data_get($item, $labelField);
                }
            }
        } elseif (!$usesAjax) {
            // Altrimenti, se non usiamo AJAX, risolviamo le choices come prima
            $choices = $this->resolveChoices($fieldConfig['choices'] ?? []);
        }

        return [
            'name' => $name,
            'value' => $currentValue,
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
