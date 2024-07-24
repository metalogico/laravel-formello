<?php

namespace Metalogico\Formello\Widgets;

class DateTimeWidget extends BaseWidget
{
    public function getTemplate(): string
    {
        return 'formello::widgets.bootstrap5.datetime';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig['attributes'] = $fieldConfig['attributes'] ?? [];
        $fieldConfig['attributes']['class'] = trim(($attributes['class'] ?? '') . ' form-control');
        $fieldConfig['attributes']['id'] = $attributes['id'] ?? $name;
        $fieldConfig['attributes']['type'] = 'datetime-local';

        $format = $fieldConfig['format'] ?? 'Y-m-d\TH:i';

        if ($value instanceof \DateTime) {
            $value = $value->format($format);
        } elseif (is_string($value)) {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date) {
                $value = $date->format('Y-m-d\TH:i');
            }
        }

        // Set step attribute for seconds if format includes seconds
        if (strpos($format, ':s') !== false) {
            $attributes['step'] = 1;
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
}
