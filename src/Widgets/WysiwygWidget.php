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
            'data-formello-wysiwyg' => json_encode($fieldConfig['jodit'] ?? []),
        ];

        $fieldConfig = $this->mergeDefaultAttributes($fieldConfig, $defaults, $name);

        return [
            'name' => $name,
            'value' => $value,
            'config' => $fieldConfig,
            'label' => $fieldConfig['label'] ?? null,
            'errors' => $errors,
        ];
    }

    public function getAssets(?array $fieldConfig = null): ?array
    {
        return [
            'scripts' => ['jodit.min.js'],
            'styles' => ['jodit.min.css'],
        ];
    }
}
