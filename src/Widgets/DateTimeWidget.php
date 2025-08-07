<?php

namespace Metalogico\Formello\Widgets;

use Metalogico\Formello\FormelloField;

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

    public function getViewData(FormelloField $field, $value, $errors = null): array
    {
        $name = $field->name;

        // Get base data from parent DateWidget
        $data = parent::getViewData($field, $value, $errors);

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
        $userFlatpickrOptions = $this->widgetConfig['flatpickr'] ?? [];
        $mergedOptions = array_merge($defaultFlatpickrOptions, $userFlatpickrOptions);

        // Update the data-formello-datepicker attribute
        $data['config']['attributes']['data-formello-datepicker'] = json_encode($mergedOptions);

        // Override format for datetime
        $format = $this->widgetConfig['format'] ?? 'Y-m-d H:i';
        $data['format'] = $format;

        // Handle datetime value formatting
        if ($value instanceof \DateTime) {
            $data['value'] = old($name, $value->format($format));
        } elseif (is_string($value) && $format !== 'Y-m-d H:i') {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date) {
                $data['value'] = old($name, $date->format('Y-m-d H:i'));
            }
        }

        return $data;
    }
}
