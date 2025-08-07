<?php

namespace Metalogico\Formello\Widgets;

use Metalogico\Formello\FormelloField;

class RadioWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'radio';
    }

    public function getViewData(FormelloField $field, $value, $errors = null): array
    {
        $name = $field->name;
        $this->widgetConfig['attributes'] = $this->widgetConfig['attributes'] ?? [];
        $this->widgetConfig['attributes']['class'] = trim(($this->widgetConfig['attributes']['class'] ?? '').' form-check-input');

        return [
            'name' => $name,
            'value' => old($name, $value),
            'label' => $field->getLabel(),
            'config' => $this->getConfig($field),
            'errors' => $errors,
            'options' => $this->getOptions(),
        ];
    }

    protected function getOptions(): array
    {
        $options = $this->widgetConfig['options'] ?? [];

        if (is_callable($options)) {
            $options = call_user_func($options);
        }

        return $options;
    }
}
