<?php

namespace Metalogico\Formello\Interfaces;

interface WidgetInterface
{
    public function render($name, $value, array $fieldConfig, $errors = null): string;
    public function getViewData($name, $value, array $fieldConfig, $errors = null): array;
    public function getTemplate(): string;
}
