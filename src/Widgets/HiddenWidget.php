<?php

namespace Metalogico\Formello\Widgets;

use Metalogico\Formello\FormelloField;

class HiddenWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'hidden';
    }

    public function getViewData(FormelloField $field, $value, $errors = null): array
    {
        return [
            'name' => $field->name,
            'value' => old($field->name, $value),
        ];
    }
}
