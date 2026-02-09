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
        // Set default values
        $defaults = [
            'class' => 'select2',
            'multiple' => false,
        ];

        $fieldConfig = $this->mergeDefaultAttributes($fieldConfig, $defaults, $name);
        if(isset($fieldConfig['multiple']) && $fieldConfig['multiple'] == true){
            $fieldConfig['multiple'] = true;
            $name .= '[]';
        }else{
            $fieldConfig['multiple'] = false;
        }

        // Extract Select2-specific configuration
        $select2Config = $fieldConfig['select2'] ?? [];
        $usesAjax = ! empty($select2Config['route']);

        $currentValue = old($name, $value);
        $choices = [];

        // If using AJAX and there is a value, load the initial option
        if ($usesAjax && ! empty($currentValue)) {
            $modelClass = $select2Config['model'] ?? null;
            $labelField = $select2Config['label_field'] ?? 'name';
            $valueField = $select2Config['value_field'] ?? 'id';

            if ($modelClass) {
                $initialItems = $modelClass::whereIn($valueField, (array) $currentValue)->get();
                foreach ($initialItems as $item) {
                    $choices[$item->$valueField] = data_get($item, $labelField);
                }
            }
        } elseif (! $usesAjax) {
            // Otherwise, if not using AJAX, resolve choices normally
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

    public function getAssets(?array $fieldConfig = null): ?array
    {
        // Deprecated: do not load Select2 assets anymore.
        return null;
    }
}
