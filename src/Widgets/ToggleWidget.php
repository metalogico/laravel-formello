<?php

namespace Metalogico\Formello\Widgets;

use Metalogico\Formello\FormelloField;

class ToggleWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'toggle';
    }

    public function getViewData(FormelloField $field, $value, $errors = null): array
    {
        $name = $field->name;
        $this->widgetConfig['attributes'] = $this->widgetConfig['attributes'] ?? [];
        $this->widgetConfig['attributes']['class'] = trim(($this->widgetConfig['attributes']['class'] ?? '').' form-check-input');
        $this->widgetConfig['attributes']['id'] = $this->widgetConfig['attributes']['id'] ?? $name;
        $this->widgetConfig['attributes']['type'] = 'checkbox';
        $this->widgetConfig['attributes']['role'] = 'switch';

        // Convert value to boolean
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        // Set checked attribute if the value is true
        if ($value) {
            $this->widgetConfig['attributes']['checked'] = 'checked';
        }

        return [
            'name' => $name,
            'value' => old($name, $value),
            'config' => $this->getConfig($field),
            'errors' => $errors,
        ];
    }
}
