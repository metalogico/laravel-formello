<?php

namespace Metalogico\Formello;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SchemaInspector
{
    public function getColumnType(Model $model, string $field): string
    {
        // 1. Check model casts first
        $casts = $model->getCasts();
        if (isset($casts[$field])) {
            return $this->normalizeCastType($casts[$field]);
        }

        // 2. Check fillable/guarded hints
        if (Str::endsWith($field, ['_id', 'Id'])) {
            return 'select';
        }

        if (in_array($field, ['email'])) {
            return 'email';
        }

        if (in_array($field, ['password', 'password_confirmation'])) {
            return 'password';
        }

        // 3. Default fallback
        return 'text';
    }

    private function normalizeCastType(string $cast): string
    {
        return match ($cast) {
            'boolean' => 'toggle',
            'date' => 'date',
            'datetime' => 'datetime',
            'timestamp' => 'datetime',
            'array' => 'checkboxes',
            'json' => 'textarea',
            default => 'text'
        };
    }
}
