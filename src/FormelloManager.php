<?php

namespace Metalogico\Formello;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ViewErrorBag;

class FormelloManager extends Formello
{

    public function __construct(Model $model = null, ViewErrorBag $errors = null)
    {
        if (!$model) {
            // Instead of creating a new Model, we'll use a null object pattern
            $model = new class extends Model {};
        }
        
        $errors = $errors ?? new ViewErrorBag;
        
        parent::__construct($model, $errors);
    }

    protected function fields(): array
    {
        return [];
    }

    protected function create(): array
    {
        return [];
    }

    protected function edit(): array
    {
        return [];
    }    

    public function getCssFramework()
    {
        return config('formello.css_framework', 'bootstrap5');
    }
}