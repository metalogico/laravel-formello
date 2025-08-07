<?php

namespace Metalogico\Formello\Widgets;

use Metalogico\Formello\FormelloField;

class WysiwygWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'wysiwyg';
    }

    public function getViewData(FormelloField $field, $value, $errors = null): array
    {
        $name = $field->name;
        $defaults = [
            'class' => 'form-control',
            'data-formello-wysiwyg' => json_encode($this->widgetConfig['jodit'] ?? []),
        ];

        $this->mergeDefaultAttributes($defaults, $name);

        return [
            'name' => $name,
            'value' => $value,
            'config' => $this->getConfig($field),
            'label' => $field->getLabel(),
            'errors' => $errors,
        ];
    }

    public function getAssets(): ?array
    {
        return [
            'scripts' => ['jodit.min.js'],
            'styles' => ['jodit.min.css'],
        ];
    }
}
