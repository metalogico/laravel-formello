<?php

namespace Metalogico\Formello\Widgets;

use Illuminate\Support\Facades\View;
use Metalogico\Formello\Interfaces\WidgetInterface;

abstract class BaseWidget implements WidgetInterface
{
    abstract public function getViewData($name, $value, array $fieldConfig, $errors = null): array;

    public function render($name, $value, array $fieldConfig, $errors = null): string
    {
        $viewData = $this->getViewData($name, $value, $fieldConfig, $errors);

        return View::make($this->getTemplate(), $viewData)->render();
    }

    public function getTemplate(): string
    {
        $framework = app('formello')->getCssFramework();

        return "formello::widgets.{$framework}.".$this->getWidgetName();
    }

    public function getWidgetName(): string
    {
        return strtolower(class_basename($this));
    }

    protected function mergeDefaultAttributes(array $fieldConfig, array $defaults, string $name): array
    {
        $fieldConfig['attributes'] = array_merge(
            $defaults,
            $fieldConfig['attributes'] ?? [],
            ['id' => $fieldConfig['attributes']['id'] ?? $name]
        );

        return $fieldConfig;
    }

    /**
     * Ensure attributes is an array with a default id and trimmed class.
     */
    protected function normalizeAttributes(array $fieldConfig, string $name, array $extra = []): array
    {
        $fieldConfig['attributes'] = array_merge(
            $extra,
            $fieldConfig['attributes'] ?? []
        );
        $fieldConfig['attributes']['class'] = trim((string) ($fieldConfig['attributes']['class'] ?? ''));
        $fieldConfig['attributes']['id'] = $fieldConfig['attributes']['id'] ?? $name;

        return $fieldConfig;
    }

    /**
     * Resolve choices from an array or callable. Also accepts the legacy `options` key.
     */
    protected function resolveChoices(mixed $choices): array
    {
        if (is_callable($choices)) {
            $choices = call_user_func($choices);
        }

        return is_array($choices) ? $choices : [];
    }

    /**
     * HTML name for array fields. Does not change the old() / error bag key.
     */
    protected function inputName(string $name, array $fieldConfig): string
    {
        if (! empty($fieldConfig['multiple']) && ! str_ends_with($name, '[]')) {
            return $name.'[]';
        }

        return $name;
    }

    protected function viewPayload(string $name, mixed $value, array $fieldConfig, $errors, array $extra = []): array
    {
        return array_merge([
            'name' => $name,
            'value' => $value,
            'label' => $fieldConfig['label'] ?? null,
            'config' => $fieldConfig,
            'errors' => $errors,
        ], $extra);
    }

    /**
     * Get assets required by this widget (optional)
     * Override in child classes to specify required assets
     *
     * @param  array|null  $fieldConfig  Optional field configuration for conditional assets
     * @return array|null Array with 'scripts' and 'styles' keys, or null if no assets needed
     */
    public function getAssets(?array $fieldConfig = null): ?array
    {
        return null;
    }
}
