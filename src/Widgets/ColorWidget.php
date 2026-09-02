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
        $fieldConfig = $this->normalizeAttributes($fieldConfig, $name, [
            'type' => 'text',
        ]);

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

        $userPickrOptions = $fieldConfig['color'] ?? [];
        $mergedOptions = array_merge($defaultPickrOptions, $userPickrOptions);

        $fieldConfig['attributes']['data-formello-colorpicker'] = json_encode($mergedOptions);

        return $this->viewPayload($name, $value, $fieldConfig, $errors);
    }

    public function getAssets(?array $fieldConfig = null): ?array
    {
        return [
            'scripts' => ['pickr.min.js'],
            'styles' => ['nano.min.css'],
        ];
    }
}
