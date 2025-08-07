<?php

namespace Metalogico\Formello\Widgets;

use Illuminate\Support\Facades\View;
use Metalogico\Formello\Interfaces\WidgetInterface;
use Metalogico\Formello\FormelloField;

abstract class BaseWidget implements WidgetInterface
{
    protected array $widgetConfig;

    public function __construct(array $config = [])
    {
        $this->widgetConfig = $config;
    }

    abstract public function getViewData(FormelloField $field, $value, $errors = null): array;

    public function render(FormelloField $field, $value, $errors = null): string
    {
        $viewData = $this->getViewData($field, $value, $errors);

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

    protected function mergeDefaultAttributes(array $defaults, string $name): array
    {
        $this->widgetConfig['attributes'] = array_merge(
            $defaults,
            $this->widgetConfig['attributes'] ?? [],
            ['id' => $this->widgetConfig['attributes']['id'] ?? $name]
        );

        return $this->widgetConfig;
    }

    /**
     * Get assets required by this widget (optional)
     * Override in child classes to specify required assets
     *
     * @return array|null Array with 'scripts' and 'styles' keys, or null if no assets needed
     */
    public function getAssets(): ?array
    {
        return null;
    }

    public function getConfig(?FormelloField $field = null): array
    {
        $fieldConfig = $field ? $field->getConfig() : [];
        return array_merge($fieldConfig, $this->widgetConfig);
    }
}
