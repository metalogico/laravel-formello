<?php

namespace Metalogico\Formello\Widgets;

class HiddenWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'hidden';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        return [
            'name' => $name,
            'value' => old($name, $value),
        ];
    }
}
