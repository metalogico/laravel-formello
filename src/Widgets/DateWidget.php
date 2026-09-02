<?php

namespace Metalogico\Formello\Widgets;

class DateWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'date';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig = $this->normalizeAttributes($fieldConfig, $name, [
            'type' => 'text',
        ]);

        $defaultFlatpickrOptions = [
            'altInput' => true,
            'altFormat' => 'd F Y',
            'dateFormat' => 'Y-m-d',
            'locale' => 'it',
        ];

        $userFlatpickrOptions = $fieldConfig['date'] ?? [];
        $mergedOptions = array_merge($defaultFlatpickrOptions, $userFlatpickrOptions);

        $hasErrors = ! empty($errors);
        if (! empty($mergedOptions['altInput'])) {
            $existingAltClass = $mergedOptions['altInputClass']
                ?? ($fieldConfig['attributes']['class'] ?? 'form-control');
            $mergedOptions['altInputClass'] = trim($existingAltClass.($hasErrors ? ' is-invalid' : ''));
        }

        $fieldConfig['attributes']['data-formello-datepicker'] = json_encode($mergedOptions);

        $format = $fieldConfig['format'] ?? 'Y-m-d';

        if ($value instanceof \DateTime) {
            $value = $value->format($format);
        } elseif (is_string($value) && $format !== 'Y-m-d') {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date) {
                $value = $date->format('Y-m-d');
            }
        }

        return $this->viewPayload($name, $value, $fieldConfig, $errors, [
            'format' => $format,
        ]);
    }

    public function getAssets(?array $fieldConfig = null): ?array
    {
        return [
            'scripts' => ['flatpickr.min.js', 'l10n/it.js'],
            'styles' => ['flatpickr.min.css'],
        ];
    }
}
