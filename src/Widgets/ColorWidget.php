<?php

namespace Metalogico\Formello\Widgets;

class ColorWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'color';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig['attributes'] = $fieldConfig['attributes'] ?? [];
        $fieldConfig['attributes']['class'] = trim(($fieldConfig['attributes']['class'] ?? '').' form-control');
        $fieldConfig['attributes']['id'] = $fieldConfig['attributes']['id'] ?? $name;
        $fieldConfig['attributes']['type'] = 'text'; // Pickr works on text inputs

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
        $userPickrOptions = $fieldConfig['pickr'] ?? [];
        $mergedOptions = array_merge($defaultPickrOptions, $userPickrOptions);

        // Pass the final options to the view
        $fieldConfig['attributes']['data-formello-colorpicker'] = json_encode($mergedOptions);

        return [
            'name' => $name,
            'value' => old($name, $value),
            'label' => $fieldConfig['label'] ?? null,
            'config' => $fieldConfig,
            'errors' => $errors,
        ];
    }

    public function getAssets(?array $fieldConfig = null): ?array
    {
        return [
            'scripts' => ['pickr.min.js'],
            'styles' => ['nano.min.css'],
        ];
    }
}
