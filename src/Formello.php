<?php

namespace Metalogico\Formello;

use Illuminate\Support\ViewErrorBag;
use Illuminate\Database\Eloquent\Model;
use Metalogico\Formello\Interfaces\WidgetInterface;

abstract class Formello
{
    protected $model;
    protected $formConfig = [];
    protected $fields = [];
    protected $errors;

    public function __construct(Model $model, ViewErrorBag $errors = null)
    {
        $this->model = $model;
        $this->errors = $errors ?? session()->get('errors', new ViewErrorBag);
        $this->initializeForm();
        $this->initializeFields();
    }

    abstract protected function fields(): array;
    abstract protected function form(): array;

    protected function initializeForm()
    {
        $this->formConfig = $this->form();
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
        $fillable = $this->model->getFillable();
        $defaults = [];
        foreach ($fillable as $field) {
            $defaults[$field] = $this->getDefaultWidgetForField($field);
        }
        return $defaults;
    }

    /**
     * Map database field types to default widgets
     */
    protected function getDefaultWidgetForField($field)
    {
        $columnType = $this->model->getConnection()
            ->getSchemaBuilder()
            ->getColumnType($this->model->getTable(), $field);

        switch ($columnType) {
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

    /**
     * Identifies the widget to use for a given field
     */
    protected function resolveWidget($widget)
    {
        if (is_null($widget)) {
            return new Widgets\TextWidget();
        }

        if (is_string($widget)) {
            if (class_exists($widget)) {
                return new $widget();
            }
            return app('formello.widgets')->get($widget) ?? new Widgets\TextWidget();
        }

        if ($widget instanceof WidgetInterface) {
            return $widget;
        }

        throw new \InvalidArgumentException("Invalid widget specification");
    }

    public function render()
    {
        return view('formello::form', [
            'formello' => $this,
            'formConfig' => $this->formConfig,
        ]);
    }

    public function renderField($name)
    {
        if (!isset($this->fields[$name])) {
            throw new \InvalidArgumentException("Field '{$name}' not found in form definition.");
        }

        $fieldConfig = $this->fields[$name];
        $widget = $fieldConfig['widget'];
        $config = $fieldConfig['config'] ?? [];

        // Retrieve the value, considering old input
        $value = old($name, $this->model->{$name} ?? null);

        // Get any errors for this field
        $errors = $this->errors->get($name);

        // If widget is a string (class name), instantiate it
        if (is_string($widget)) {
            $widget = new $widget();
        }

        // Ensure the widget implements WidgetInterface
        if (!$widget instanceof WidgetInterface) {
            throw new \InvalidArgumentException("Widget for field '{$name}' must implement WidgetInterface.");
        }

        // Render the widget
        return $widget->render($name, $value, $config, $errors);
    }

    public function getFields()
    {
        return $this->fields;
    }
}
