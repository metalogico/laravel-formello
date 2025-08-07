<?php

namespace Metalogico\Formello\Widgets;

use Metalogico\Formello\FormelloField;

class ColorWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'color';
    }

    public function getViewData(FormelloField $field, $value, $errors = null): array
    {
        $name = $field->name;
        $this->widgetConfig['attributes'] = $this->widgetConfig['attributes'] ?? [];
        $this->widgetConfig['attributes']['class'] = trim(($this->widgetConfig['attributes']['class'] ?? '').' form-control');
        $this->widgetConfig['attributes']['id'] = $this->widgetConfig['attributes']['id'] ?? $name;
        $this->widgetConfig['attributes']['type'] = 'text'; // Pickr works on text inputs

        // Define default Pickr options
        $defaultPickrOptions = [
            'theme' => 'nano',
            'default' => $value ?: '#3498db',
            'components' => [
                'preview' => true,
                'opacity' => true,
                'hue' => true,
                'interaction' => [
                    'hex' => true,
                    'rgba' => true,
                    'input' => true,
                    'clear' => true,
                    'save' => true,
                ],
            ],
        ];

        // Merge default options with user-provided options
        $userPickrOptions = $this->widgetConfig['pickr'] ?? [];
        $mergedOptions = array_merge($defaultPickrOptions, $userPickrOptions);

        // Pass the final options to the view
        $this->widgetConfig['attributes']['data-formello-colorpicker'] = json_encode($mergedOptions);

        return [
            'name' => $name,
            'value' => old($name, $value),
            'label' => $field->getLabel(),
            'config' => $this->getConfig($field),
            'errors' => $errors,
        ];
    }

    public function getAssets(): ?array
    {
        return [
            'scripts' => ['pickr.min.js'],
            'styles' => ['nano.min.css'],
        ];
    }
}
