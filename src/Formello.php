<?php

namespace Metalogico\Formello;

use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Database\Eloquent\Model;
use Metalogico\Formello\Interfaces\WidgetInterface;
use Illuminate\Support\Facades\Schema;

abstract class Formello
{
    protected Model $model;
    protected ViewErrorBag $errors;
    protected array $formConfig = [];
    protected array $fields = [];
    private WidgetFactory $widgetFactory;
    private SchemaInspector $schemaInspector;

    public function __construct(
        Model $model, 
        ViewErrorBag $errors = null,
        WidgetFactory $widgetFactory = null,
        SchemaInspector $schemaInspector = null
    ) {
        $this->model = $model;
        $this->errors = $errors ?? session()->get('errors', new ViewErrorBag);
        $this->widgetFactory = $widgetFactory ?? new WidgetFactory();
        $this->schemaInspector = $schemaInspector ?? new SchemaInspector();
        
        $this->initializeForm();
        $this->initializeFields();
    }

    abstract protected function fields(): array;
    abstract protected function create(): array;
    abstract protected function edit(): array;

    protected function initializeFields(): void
    {
        $definedFields = $this->fields();

        foreach ($definedFields as $name => $fieldConfig) {
            $widget = $this->resolveWidget($fieldConfig, $name);

            $this->fields[$name] = [
                'widget' => $widget,
                'config' => $fieldConfig,
            ];
        }
    }

    protected function resolveWidget(array $fieldConfig, string $fieldName): WidgetInterface
    {
        // Se widget specificato esplicitamente
        if (isset($fieldConfig['widget'])) {
            if (is_string($fieldConfig['widget']) && class_exists($fieldConfig['widget'])) {
                return new $fieldConfig['widget']();
            }
            if ($fieldConfig['widget'] instanceof WidgetInterface) {
                return $fieldConfig['widget'];
            }
        }

        // Auto-detect dal database schema
        $columnType = $this->schemaInspector->getColumnType($this->model, $fieldName);
        return $this->widgetFactory->make($columnType);
    }

    /**
     * Initialize the fields
     */
    protected function initializeFields()
    {
        $defaultFields = $this->getDefaultFields();
        $definedFields = $this->fields();

        foreach ($defaultFields as $name => $defaultWidget) {
            $fieldConfig = $definedFields[$name] ?? [];
            $widget = $this->resolveWidget($fieldConfig['widget'] ?? $defaultWidget);

            $this->fields[$name] = [
                'widget' => $widget,
                'config' => $fieldConfig,
            ];
        }
    }

    protected function getDefaultFields()
    {
        $defaults = [];
        foreach ($this->fields() as $field => $config) {
            $defaults[$field] = $this->getDefaultWidgetForField($field);
        }
        return $defaults;
    }

    /**
     * Map database field types to default widgets
     */
    protected function getDefaultWidgetForField($field)
    {
        // checks if the column exists and gets its type
        $schema = $this->model->getConnection()->getSchemaBuilder();
        if ($schema->hasColumn($this->model->getTable(), $field)) {
            $columnType = $schema->getColumnType($this->model->getTable(), $field);
        } else {
            $columnType = 'string';
        }

        switch ($columnType) {
            case 'char':
            case 'varchar':
            case 'string':
                return new Widgets\TextWidget();
            case 'text':
                return new Widgets\TextareaWidget();
            case 'boolean':
            case 'tinyint':
                return new Widgets\ToggleWidget();
            case 'date':
                return new Widgets\DateWidget();
            case 'datetime':
            case 'timestamp':
                return new Widgets\DateTimeWidget();
            default:
                return new Widgets\TextWidget();
        }
    }

    public function render()
    {
        return view('formello::form', [
            'formello' => $this,
            'formConfig' => $this->formConfig,
        ]);
    }

    public function renderField(string $name): string
    {
        if (!isset($this->fields[$name])) {
            throw new \InvalidArgumentException("Field '{$name}' not found");
        }

        $fieldConfig = $this->fields[$name];
        $widget = $fieldConfig['widget'];
        $config = $fieldConfig['config'];
        
        $value = old($name, $config['value'] ?? $this->model->{$name} ?? null);
        $errors = $this->errors->get($name);

        return $widget->render($name, $value, $config, $errors);
    }

    public function getCssFramework()
    {
        return config('formello.css_framework', 'bootstrap5');
    }

    public function getFields()
    {
        return $this->fields;
    }

}
