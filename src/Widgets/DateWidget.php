<?php

namespace Metalogico\Formello\Widgets;

use Metalogico\Formello\Widgets\BaseWidget;

class DateWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'date';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig['attributes'] = $fieldConfig['attributes'] ?? [];
        $fieldConfig['attributes']['class'] = trim(($attributes['class'] ?? '') . ' form-control');
        $fieldConfig['attributes']['id'] = $attributes['id'] ?? $name;
        $attributes['type'] = 'date';

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
}
