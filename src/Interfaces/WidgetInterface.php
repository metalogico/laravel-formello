<?php

namespace Metalogico\Formello\Interfaces;

use Metalogico\Formello\FormelloField;

interface WidgetInterface
{
    public function render(FormelloField $field, $value, $errors = null): string;

    public function getViewData(FormelloField $field, $value, $errors = null): array;

    public function getTemplate(): string;

    public function getWidgetName(): string;
    
    public function getAssets(): ?array;

    public function getConfig(?FormelloField $field = null): array;
}
