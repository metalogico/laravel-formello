<?php

namespace Metalogico\Formello\Widgets;

class TextareaWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'textarea';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig = $this->normalizeAttributes($fieldConfig, $name);

        return $this->viewPayload($name, $value, $fieldConfig, $errors);
    }
}
