<?php

namespace Metalogico\Formello\Widgets;

class SelectWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'select';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig = $this->normalizeAttributes($fieldConfig, $name);

        if (! empty($fieldConfig['multiple'])) {
            $fieldConfig['attributes']['multiple'] = 'multiple';
        }

        return $this->viewPayload(
            $this->inputName($name, $fieldConfig),
            $value,
            $fieldConfig,
            $errors,
            ['choices' => $this->resolveChoices($fieldConfig['choices'] ?? [])]
        );
    }
}
