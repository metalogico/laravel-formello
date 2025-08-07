<?php

namespace Metalogico\Formello\Widgets;

use Metalogico\Formello\FormelloField;

class RangeWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'range';
    }

    public function getViewData(FormelloField $field, $value, $errors = null): array
    {
        $name = $field->name;
        $this->widgetConfig['attributes'] = $this->widgetConfig['attributes'] ?? [];
        $this->widgetConfig['attributes']['class'] = trim(($this->widgetConfig['attributes']['class'] ?? '').' form-range');
        $this->widgetConfig['attributes']['type'] = 'range';
        $this->widgetConfig['attributes']['id'] = $this->widgetConfig['attributes']['id'] ?? $name;

        // Set default min, max, and step if not provided
        $this->widgetConfig['attributes']['min'] = $this->widgetConfig['attributes']['min'] ?? 0;
        $this->widgetConfig['attributes']['max'] = $this->widgetConfig['attributes']['max'] ?? 100;
        $this->widgetConfig['attributes']['step'] = $this->widgetConfig['attributes']['step'] ?? 1;

        return [
            'name' => $name,
            'value' => old($name, $value),
            'label' => $field->getLabel(),
            'config' => $this->getConfig($field),
            'errors' => $errors,
            'showValue' => $this->widgetConfig['showValue'] ?? true,
        ];
    }
}
