<?php

namespace Metalogico\Formello;

use Illuminate\Database\Eloquent\Model;

class SchemaInspector
{
    private array $columnTypeCache = [];
    
    public function getColumnType(Model $model, string $field): string
    {
        $table = $model->getTable();
        $cacheKey = "{$table}.{$field}";
        
        if (isset($this->columnTypeCache[$cacheKey])) {
            return $this->columnTypeCache[$cacheKey];
        }
        
        $schema = $model->getConnection()->getSchemaBuilder();
        
        if (!$schema->hasColumn($table, $field)) {
            return $this->columnTypeCache[$cacheKey] = 'string';
        }
        
        $type = $schema->getColumnType($table, $field);
        return $this->columnTypeCache[$cacheKey] = $this->normalizeType($type);
    }
    
    private function normalizeType(string $type): string
    {
        return match($type) {
            'varchar', 'char' => 'string',
            'tinyint' => 'boolean',
            'timestamp' => 'datetime',
            default => $type
        };
    }
}