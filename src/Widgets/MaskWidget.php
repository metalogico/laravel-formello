<?php

namespace Metalogico\Formello\Widgets;

use Metalogico\Formello\FormelloField;

class MaskWidget extends TextWidget
{
    public function getWidgetName(): string
    {
        return 'mask';
    }

    public function getTemplate(): string
    {
        // Use the same template as DateWidget since they're identical
        $framework = app('formello')->getCssFramework();

        return "formello::widgets.{$framework}.text";
    }

    public function getViewData(FormelloField $field, $value, $errors = null): array
    {
        $name = $field->name;
        // Get base data from TextWidget
        $viewData = parent::getViewData($field, $value, $errors);

        // Add mask data attribute if mask is configured
        if (isset($this->widgetConfig['mask'])) {
            $viewData['config']['attributes']['data-formello-mask'] = json_encode($this->widgetConfig['mask']);
        }

        return $viewData;
    }

    /**
     * Get assets for MaskWidget - always returns IMask assets
     */
    public function getAssets(): ?array
    {
        return [
            'scripts' => ['imask.min.js'],
            'styles' => [],
        ];
    }
}
