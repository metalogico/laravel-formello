<?php

namespace Metalogico\Formello\Widgets;

class RadioWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'radio';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig = $this->normalizeAttributes($fieldConfig, $name);

        return $this->viewPayload($name, $value, $fieldConfig, $errors, [
            'choices' => $this->resolveChoices($fieldConfig['choices'] ?? ($fieldConfig['options'] ?? [])),
        ]);
    }
}
