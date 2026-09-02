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
        $fieldConfig = $this->normalizeAttributes($fieldConfig, $name);

        $multiple = ! empty($fieldConfig['multiple']);
        if ($multiple) {
            $fieldConfig['attributes']['multiple'] = 'multiple';
        }

        // Tom Select options
        $tsConfig = $fieldConfig['tomselect'] ?? [];
        $usesAjax = ! empty($tsConfig['route']);

        // Normalize option keys (accept snake_case and camelCase)
        $searchFieldOpt = $tsConfig['searchField'] ?? ($tsConfig['search_field'] ?? 'text');
        // For AJAX, we must match backend payload fields: id/text
        $valueFieldOpt = $usesAjax ? 'id' : ($tsConfig['valueField'] ?? ($tsConfig['value_field'] ?? 'id'));
        $labelFieldOpt = $usesAjax ? 'text' : ($tsConfig['labelField'] ?? ($tsConfig['label_field'] ?? 'text'));

        // Default options passed to JS
        $defaultOptions = [
            'placeholder' => $tsConfig['placeholder'] ?? ($fieldConfig['placeholder'] ?? __('Select')),
            'maxOptions' => $tsConfig['maxOptions'] ?? 100,
            'searchField' => $searchFieldOpt,
            'valueField' => $valueFieldOpt,
            'labelField' => $labelFieldOpt,
            'create' => $tsConfig['create'] ?? false,
            'dropdownParent' => $tsConfig['dropdownParent'] ?? ($tsConfig['dropdown_parent'] ?? 'body'),
        ];

        if ($usesAjax) {
            $dependsOn = $tsConfig['depends_on'] ?? null;
            $minLen = $tsConfig['minLength'] ?? ($tsConfig['min_length'] ?? 0);

            $defaultOptions['preload'] = $tsConfig['preload'] ?? 'focus';
            $defaultOptions['ajax'] = [
                'url' => $tsConfig['route'],
                'delay' => $tsConfig['delay'] ?? 250,
                'depends_on' => $dependsOn,
                'depends_param' => $tsConfig['depends_param'] ?? $dependsOn,
                'minLength' => $minLen,
            ];
        }

        $fieldConfig['attributes']['data-formello-tomselect'] = json_encode($defaultOptions);

        $choices = [];

        // Preload initial options when AJAX is used and there is a current value
        if ($usesAjax && ! empty($value)) {
            $modelClass = $tsConfig['model'] ?? null;
            $labelField = $tsConfig['label_field'] ?? 'name';
            $valueField = $tsConfig['value_field'] ?? 'id';

            if ($modelClass && class_exists($modelClass)) {
                $ids = (array) $value;
                $initialItems = $modelClass::whereIn($valueField, $ids)->get();
                foreach ($initialItems as $item) {
                    $choices[$item->$valueField] = data_get($item, $labelField);
                }
            }
        } elseif (! $usesAjax) {
            $choices = $this->resolveChoices($fieldConfig['choices'] ?? []);
        }

        return $this->viewPayload(
            $this->inputName($name, $fieldConfig),
            $value,
            $fieldConfig,
            $errors,
            [
                'choices' => $choices,
                'usesAjax' => $usesAjax,
            ]
        );
    }

    public function getAssets(?array $fieldConfig = null): ?array
    {
        return [
            'scripts' => ['tom-select.complete.js'],
            'styles' => ['tom-select.default.min.css'],
        ];
    }
}
