<?php

namespace Metalogico\Formello\Widgets;

use Metalogico\Formello\Interfaces\WidgetInterface;
use Illuminate\Support\Facades\View;

abstract class BaseWidget implements WidgetInterface
{
    abstract public function getTemplate(): string;
    abstract public function getViewData($name, $value, array $fieldConfig, $errors = null): array;

    public function render($name, $value, array $fieldConfig, $errors = null): string
    {
        $viewData = $this->getViewData($name, $value, $fieldConfig, $errors);
        return View::make($this->getTemplate(), $viewData)->render();
    }
}
