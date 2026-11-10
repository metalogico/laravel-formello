<?php

namespace Metalogico\Formello\Widgets;

class CheckboxesWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'checkboxes';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig = $this->normalizeAttributes($fieldConfig, $name);

        return $this->viewPayload($name, $value, $fieldConfig, $errors, [
            'choices' => $this->resolveChoices($fieldConfig['choices'] ?? []),
        ]);
    }
}
