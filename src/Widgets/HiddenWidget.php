<?php

namespace Metalogico\Formello\Widgets;

class HiddenWidget extends BaseWidget
{
    public function getTemplate(): string
    {
        return 'formello::widgets.bootstrap5.hidden';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        return [
            'name' => $name,
            'value' => old($name, $value),
        ];
    }
}
