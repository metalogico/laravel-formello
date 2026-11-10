<?php

namespace Metalogico\Formello\Widgets;

class DateTimeWidget extends DateWidget
{
    public function getWidgetName(): string
    {
        return 'datetime';
    }

    public function getTemplate(): string
    {
        // Use the same template as DateWidget since they're identical
        $framework = app('formello')->getCssFramework();

        return "formello::widgets.{$framework}.date";
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        // Get base data from parent DateWidget
        $data = parent::getViewData($name, $value, $fieldConfig, $errors);

        // Override Flatpickr options for datetime
        $defaultFlatpickrOptions = [
            'altInput' => true,
            'altFormat' => 'd F Y H:i',
            'dateFormat' => 'Y-m-d H:i',
            'locale' => 'it',
            'enableTime' => true,
            'time_24hr' => true,
        ];

        // Merge with user options
        $userFlatpickrOptions = $fieldConfig['datetime'] ?? [];
        $mergedOptions = array_merge($defaultFlatpickrOptions, $userFlatpickrOptions);

        // Propagate validation state to Flatpickr's alt input
        $hasErrors = !empty($errors);
        if (!empty($mergedOptions['altInput'])) {
            $existingAltClass = $mergedOptions['altInputClass']
                ?? ($data['config']['attributes']['class'] ?? 'form-control');
            $mergedOptions['altInputClass'] = trim($existingAltClass . ($hasErrors ? ' is-invalid' : ''));
        }

        // Update the data-formello-datepicker attribute
        $data['config']['attributes']['data-formello-datepicker'] = json_encode($mergedOptions);

        // Override format for datetime
        $format = $fieldConfig['format'] ?? 'Y-m-d H:i';
        $data['format'] = $format;

        // Handle datetime value formatting
        if ($value instanceof \DateTime) {
            $data['value'] = $value->format($format);
        } elseif (is_string($value) && $format !== 'Y-m-d H:i') {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date) {
                $data['value'] = $date->format('Y-m-d H:i');
            }
        }

        return $data;
    }
}
