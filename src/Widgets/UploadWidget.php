<?php

namespace Metalogico\Formello\Widgets;

use Metalogico\Formello\FormelloField;

class UploadWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'upload';
    }

    public function getViewData(FormelloField $field, $value, $errors = null): array
    {
        $name = $field->name;
        $this->widgetConfig['attributes'] = $this->widgetConfig['attributes'] ?? [];
        $this->widgetConfig['attributes']['class'] = trim(($this->widgetConfig['attributes']['class'] ?? '').' form-control');
        $this->widgetConfig['attributes']['id'] = $this->widgetConfig['attributes']['id'] ?? $name;
        $this->widgetConfig['attributes']['type'] = $this->widgetConfig['type'] ?? 'file';

        return [
            'name' => $name,
            'value' => $value,
            'label' => $field->getLabel(),
            'config' => $this->getConfig($field),
            'errors' => $errors,
        ];
    }
}
