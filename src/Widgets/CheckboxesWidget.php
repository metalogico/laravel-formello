<?php

namespace Metalogico\Formello\Widgets;

use Metalogico\Formello\FormelloField;

class CheckboxesWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'checkboxes';
    }

    public function getViewData(FormelloField $field, $value, $errors = null): array
    {
        $name = $field->name;
        $this->widgetConfig['attributes'] = $this->widgetConfig['attributes'] ?? [];
        $this->widgetConfig['attributes']['id'] = $this->widgetConfig['attributes']['id'] ?? $name;

        $choices = $this->resolveChoices($this->widgetConfig['choices']) ?? [];

        return [
            'name' => $name,
            'value' => old($name, $value),
            'label' => $field->getLabel(),
            'config' => $this->getConfig($field),
            'errors' => $errors,
            'choices' => $choices,
        ];
    }

    protected function resolveChoices($choices)
    {
        if (is_callable($choices)) {
            return call_user_func($choices);
        }

        return $choices;
    }
}
