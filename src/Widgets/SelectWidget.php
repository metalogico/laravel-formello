<?php

namespace Metalogico\Formello\Widgets;

use Metalogico\Formello\FormelloField;

class SelectWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'select';
    }

    public function getViewData(FormelloField $field, $value, $errors = null): array
    {
        $name = $field->name;
        $this->widgetConfig['attributes'] = $this->widgetConfig['attributes'] ?? [];
        $this->widgetConfig['attributes']['class'] = trim(($this->widgetConfig['attributes']['class'] ?? '').' form-select');
        $this->widgetConfig['attributes']['id'] = $this->widgetConfig['attributes']['id'] ?? $name;

        // Add support for multiple selection
        if (! empty($this->widgetConfig['multiple'])) {
            $this->widgetConfig['attributes']['multiple'] = 'multiple';
            $name .= '[]'; // Modify name to handle array submission
        }

        $choices = $this->resolveChoices($this->widgetConfig['choices'] ?? []);

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

    public function getAssets(): ?array
    {
        return [
            'scripts' => ['select2.min.js'],
            'styles' => ['select2.min.css'],
        ];
    }
}
