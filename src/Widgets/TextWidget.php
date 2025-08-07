<?php

namespace Metalogico\Formello\Widgets;

use Metalogico\Formello\FormelloField;

class TextWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'text';
    }

    public function getViewData(FormelloField $field, $value, $errors = null): array
    {
        $name = $field->name;
        $this->widgetConfig['attributes'] = $this->widgetConfig['attributes'] ?? [];
        $this->widgetConfig['attributes']['class'] = trim(($this->widgetConfig['attributes']['class'] ?? '').' form-control');
        $this->widgetConfig['attributes']['id'] = $this->widgetConfig['attributes']['id'] ?? $name;
        $this->widgetConfig['attributes']['type'] = $this->widgetConfig['type'] ?? 'text';

        $typeAttributes = match ($this->widgetConfig['attributes']['type']) {
            'number' => ['inputmode' => 'numeric', 'pattern' => '[0-9]*'],
            'email' => ['autocomplete' => 'email'],
            'password' => ['autocomplete' => 'new-password'],
            default => []
        };

        $this->widgetConfig['attributes'] = array_merge($this->widgetConfig['attributes'], $typeAttributes);

        $safeValue = $value;
        if ($this->widgetConfig['attributes']['type'] === 'password') {
            $safeValue = '';
        }

        return [
            'name' => $name,
            'value' => old($name, $safeValue),
            'label' => $field->getLabel(),
            'config' => $this->getConfig($field),
            'errors' => $errors,
        ];
    }
}
