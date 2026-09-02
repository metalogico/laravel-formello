<?php

namespace Metalogico\Formello\Widgets;

class ToggleWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'toggle';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig = $this->normalizeAttributes($fieldConfig, $name, [
            'type' => 'checkbox',
            'role' => 'switch',
        ]);
        unset($fieldConfig['attributes']['checked']);

        $checked = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        return $this->viewPayload($name, $value, $fieldConfig, $errors, [
            'checked' => $checked,
        ]);
    }
}
