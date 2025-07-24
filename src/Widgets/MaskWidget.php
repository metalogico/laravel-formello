<?php

namespace Metalogico\Formello\Widgets;

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

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        // Get base data from TextWidget
        $viewData = parent::getViewData($name, $value, $fieldConfig, $errors);

        // Add mask data attribute if mask is configured
        if (isset($fieldConfig['mask'])) {
            $viewData['config']['attributes']['data-formello-mask'] = json_encode($fieldConfig['mask']);
        }

        return $viewData;
    }

    /**
     * Get assets for MaskWidget - always returns IMask assets
     */
    public function getAssets(?array $fieldConfig = null): ?array
    {
        return [
            'scripts' => ['imask.min.js'],
            'styles' => [],
        ];
    }
}
