<?php

namespace Metalogico\Formello\Widgets;

class WysiwygWidget extends BaseWidget
{
    public function getWidgetName(): string
    {
        return 'wysiwyg';
    }

    public function getViewData($name, $value, array $fieldConfig, $errors = null): array
    {
        $defaults = [
            'data-formello-wysiwyg' => json_encode($fieldConfig['wysiwyg'] ?? []),
        ];

        $fieldConfig = $this->mergeDefaultAttributes($fieldConfig, $defaults, $name);

        return $this->viewPayload($name, $value, $fieldConfig, $errors);
    }

    public function getAssets(?array $fieldConfig = null): ?array
    {
        return [
            'scripts' => ['jodit.min.js'],
            'styles' => ['jodit.min.css'],
        ];
    }
}
