<?php

namespace Metalogico\Formello\Widgets;

class TomSelectWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'tomselect';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig['attributes'] = $fieldConfig['attributes'] ?? [];
        $fieldConfig['attributes']['class'] = trim(($fieldConfig['attributes']['class'] ?? ''));
        $fieldConfig['attributes']['id'] = $fieldConfig['attributes']['id'] ?? $name;

        // multiple support
        $multiple = !empty($fieldConfig['multiple']);
        if ($multiple) {
            $fieldConfig['attributes']['multiple'] = 'multiple';
            $name .= '[]';
        }

        // Tom Select options
        $tsConfig = $fieldConfig['tomselect'] ?? [];
        $usesAjax = !empty($tsConfig['route']);

        // Default options passed to JS
        $defaultOptions = [
            'placeholder' => $tsConfig['placeholder'] ?? ($fieldConfig['placeholder'] ?? __('Select')),
            'maxOptions' => $tsConfig['maxOptions'] ?? 100,
            'searchField' => $tsConfig['searchField'] ?? 'text',
            'valueField' => $tsConfig['valueField'] ?? 'id',
            'labelField' => $tsConfig['labelField'] ?? 'text',
            'create' => false,
        ];

        if ($usesAjax) {
            $defaultOptions['ajax'] = [
                'url' => $tsConfig['route'],
                'delay' => $tsConfig['delay'] ?? 250,
                'depends_on' => $tsConfig['depends_on'] ?? null,
                'depends_param' => $tsConfig['depends_param'] ?? ($tsConfig['depends_on'] ?? null),
                'minLength' => $tsConfig['minLength'] ?? 2,
            ];
        }

        $fieldConfig['attributes']['data-formello-tomselect'] = json_encode($defaultOptions);

        $currentValue = old($name, $value);
        $choices = [];

        // Preload initial options when AJAX is used and there is a current value
        if ($usesAjax && !empty($currentValue)) {
            $modelClass = $tsConfig['model'] ?? null;
            $labelField = $tsConfig['label_field'] ?? 'name';
            $valueField = $tsConfig['value_field'] ?? 'id';

            if ($modelClass && class_exists($modelClass)) {
                $ids = (array) $currentValue;
                $initialItems = $modelClass::whereIn($valueField, $ids)->get();
                foreach ($initialItems as $item) {
                    $choices[$item->$valueField] = data_get($item, $labelField);
                }
            }
        } elseif (!$usesAjax) {
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
        $framework = config('formello.css_framework', 'bootstrap5');
        $styles = ['tom-select.default.min.css'];
        if ($framework === 'tailwindcss4') {
            $styles[] = 'tom-select.tailwind.css';
        }

        return [
            'scripts' => ['tom-select.complete.js'],
            'styles' => $styles,
        ];
    }
}
