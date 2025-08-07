<?php

namespace Metalogico\Formello\Widgets;

use Metalogico\Formello\FormelloField;

class TextareaWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'textarea';
    }

    public function getViewData(FormelloField $field, $value, $errors = null): array
    {
        $name = $field->name;
        $this->widgetConfig['attributes'] = $this->widgetConfig['attributes'] ?? [];
        $this->widgetConfig['attributes']['class'] = trim(($this->widgetConfig['attributes']['class'] ?? '').' form-control');
        $this->widgetConfig['attributes']['id'] = $this->widgetConfig['attributes']['id'] ?? $name;
        $this->widgetConfig['attributes']['type'] = 'text';

        return [
            'name' => $name,
            'value' => old($name, $value),
            'label' => $field->getLabel(),
            'config' => $this->getConfig($field),
            'errors' => $errors,
        ];
    }
}
