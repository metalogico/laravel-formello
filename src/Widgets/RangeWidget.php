<?php

namespace Metalogico\Formello\Widgets;

class RangeWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'range';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig = $this->normalizeAttributes($fieldConfig, $name, [
            'type' => 'range',
            'min' => 0,
            'max' => 100,
            'step' => 1,
        ]);

        return $this->viewPayload($name, $value, $fieldConfig, $errors, [
            'showValue' => $fieldConfig['showValue'] ?? true,
        ]);
    }
}
