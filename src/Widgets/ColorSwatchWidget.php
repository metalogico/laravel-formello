<?php

namespace Metalogico\Formello\Widgets;

use Metalogico\Formello\FormelloField;

class ColorSwatchWidget extends ColorWidget
{
    public function getWidgetName(): string
    {
        return 'colorswatch';
    }

    public function getTemplate(): string
    {
        // Use the same template as ColorWidget since they're identical
        $framework = app('formello')->getCssFramework();

        return "formello::widgets.{$framework}.color";
    }

    public function getViewData(FormelloField $field, $value, $errors = null): array
    {
        // Get base data from parent ColorWidget
        $data = parent::getViewData($field, $value, $errors);

        // Override Pickr options for swatches-only mode
        $defaultPickrOptions = [
            'theme' => 'nano',
            'default' => $value ?: '#3498db',
            'closeOnScroll' => true,
            'autoReposition' => true,

            // Disable picker components but keep minimal interaction
            'components' => [
                'preview' => false,
                'opacity' => false,
                'hue' => false,
                'interaction' => [
                    'hex' => false,
                    'rgba' => false,
                    'hsla' => false,
                    'hsva' => false,
                    'cmyk' => false,
                    'input' => false,
                    'clear' => false,
                    'save' => false,
                ],
            ],

            // Default swatches (can be overridden by user)
            'swatches' => [
                '#f44336', '#e91e63', '#9c27b0', '#673ab7',
                '#3f51b5', '#2196f3', '#03a9f4', '#00bcd4',
                '#009688', '#4caf50', '#8bc34a', '#cddc39',
                '#ffeb3b', '#ffc107', '#ff9800', '#ff5722',
                '#795548', '#9e9e9e', '#607d8b', '#000000',
            ],
        ];

        // Merge with user options (user can override swatches)
        $userPickrOptions = $this->widgetConfig['pickr'] ?? [];
        $mergedOptions = array_merge($defaultPickrOptions, $userPickrOptions);

        // Update the data-formello-colorpicker attribute
        $data['config']['attributes']['data-formello-colorpicker'] = json_encode($mergedOptions);

        return $data;
    }
}
