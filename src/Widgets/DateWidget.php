<?php

namespace Metalogico\Formello\Widgets;

use Metalogico\Formello\FormelloField;

class DateWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'date';
    }

    public function getViewData(FormelloField $field, $value, $errors = null): array
    {
        $name = $field->name;
        $this->widgetConfig['attributes'] = $this->widgetConfig['attributes'] ?? [];
        $this->widgetConfig['attributes']['class'] = trim(($this->widgetConfig['attributes']['class'] ?? '').' form-control');
        $this->widgetConfig['attributes']['id'] = $this->widgetConfig['attributes']['id'] ?? $name;
        $this->widgetConfig['attributes']['type'] = 'text'; // Flatpickr works on text inputs

        // Define default Flatpickr options
        $defaultFlatpickrOptions = [
            'altInput' => true,
            'altFormat' => 'd F Y',
            'dateFormat' => 'Y-m-d',
            'locale' => 'it',
        ];

        // Merge default options with user-provided options
        $userFlatpickrOptions = $this->widgetConfig['flatpickr'] ?? [];
        $mergedOptions = array_merge($defaultFlatpickrOptions, $userFlatpickrOptions);

        // Pass the final options to the view
        $this->widgetConfig['attributes']['data-formello-datepicker'] = json_encode($mergedOptions);

        $format = $this->widgetConfig['format'] ?? 'Y-m-d';

        if ($value instanceof \DateTime) {
            $value = $value->format($format);
        } elseif (is_string($value) && $format !== 'Y-m-d') {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date) {
                $value = $date->format('Y-m-d');
            }
        }

        return [
            'name' => $name,
            'value' => old($name, $value),
            'label' => $field->getLabel(),
            'config' => $this->getConfig($field),
            'errors' => $errors,
            'format' => $format,
        ];
    }

    public function getAssets(): ?array
    {
        return [
            'scripts' => ['flatpickr.min.js', 'l10n/it.js'],
            'styles' => ['flatpickr.min.css'],
        ];
    }
}
