<?php

namespace Metalogico\Formello\Widgets;

use Metalogico\Formello\Formello;
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
        $framework = app(Formello::class)->getCssFramework();
        return "formello::widgets.{$framework}." . $this->getWidgetName();
    }

    protected function getWidgetName(): string
    {
        return strtolower(class_basename($this));
    }
}
