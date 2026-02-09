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
        $fieldConfig['attributes'] = $fieldConfig['attributes'] ?? [];
        $fieldConfig['attributes']['class'] = trim(($fieldConfig['attributes']['class'] ?? ''));
        $fieldConfig['attributes']['id'] = $fieldConfig['attributes']['id'] ?? $name;
        $fieldConfig['attributes']['type'] = 'text'; // Flatpickr works on text inputs

        // Define default Flatpickr options
        $defaultFlatpickrOptions = [
            'altInput' => true,
            'altFormat' => 'd F Y',
            'dateFormat' => 'Y-m-d',
            'locale' => 'it',
        ];

        // Merge default options with user-provided options
        $userFlatpickrOptions = $fieldConfig['flatpickr'] ?? [];
        $mergedOptions = array_merge($defaultFlatpickrOptions, $userFlatpickrOptions);

        // Propagate validation state to Flatpickr's alt input
        $hasErrors = !empty($errors);
        if (!empty($mergedOptions['altInput'])) {
            $existingAltClass = $mergedOptions['altInputClass']
                ?? ($fieldConfig['attributes']['class'] ?? 'form-control');
            $mergedOptions['altInputClass'] = trim($existingAltClass . ($hasErrors ? ' is-invalid' : ''));
        }

        // Pass the final options to the view
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

        return [
            'name' => $name,
            'value' => old($name, $value),
            'label' => $fieldConfig['label'] ?? null,
            'config' => $fieldConfig,
            'errors' => $errors,
            'format' => $format,
        ];
    }

    public function getAssets(?array $fieldConfig = null): ?array
    {
        return [
            'scripts' => ['flatpickr.min.js', 'l10n/it.js'],
            'styles' => ['flatpickr.min.css'],
        ];
    }
}
