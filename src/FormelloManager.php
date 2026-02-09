<?php

namespace Metalogico\Formello;

/**
 * Lightweight proxy bound as app('formello').
 *
 * Before a real Formello form instance is rendered, widgets may call
 * app('formello')->getCssFramework(). This class provides a safe
 * default that reads from config without requiring a model or fields.
 */
class FormelloManager
{
    public function getCssFramework(): string
    {
        return config('formello.css_framework', 'bootstrap5');
    }
}
