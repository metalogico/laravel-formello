<?php

namespace Metalogico\Formello\Widgets;

class UploadWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'upload';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $fieldConfig = $this->normalizeAttributes($fieldConfig, $name, [
            'type' => $fieldConfig['type'] ?? 'file',
        ]);

        return $this->viewPayload($name, $value, $fieldConfig, $errors);
    }
}
