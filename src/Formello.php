<?php

namespace Metalogico\Formello;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ViewErrorBag;
use Metalogico\Formello\Interfaces\WidgetInterface;
use Metalogico\Formello\Widgets\UploadWidget;

abstract class Formello
{
    public string $formMode;

    protected Model $model;

    protected ViewErrorBag $errors;

    protected array $formConfig = [];

    protected array $fields = [];

    private WidgetFactory $widgetFactory;

    private SchemaInspector $schemaInspector;

    /**
     * Creates a new instance of Formello.
     *
     * @param  Model|string  $model  Model instance or class-string
     * @param  ViewErrorBag|null  $errors  Validation errors bag
     * @param  WidgetFactory|null  $widgetFactory  Widget factory
     * @param  SchemaInspector|null  $schemaInspector  Database schema inspector
     */
    public function __construct(
        Model|string $model,
        ?ViewErrorBag $errors = null,
        ?WidgetFactory $widgetFactory = null,
        ?SchemaInspector $schemaInspector = null
    ) {
        // Se è una stringa, creiamo una nuova istanza del modello
        if (is_string($model)) {
            $this->model = new $model;
        } else {
            $this->model = $model;
        }
        $this->errors = $errors ?? session()->get('errors', new ViewErrorBag);
        $this->widgetFactory = $widgetFactory ?? new WidgetFactory;
        $this->schemaInspector = $schemaInspector ?? new SchemaInspector;

        // Set the form mode based on the model's existence
        if ($this->model->exists) {
            $this->setFormMode('edit');
        } else {
            $this->setFormMode('create');
        }

        $this->initializeFields();
        $this->initializeForm();
    }

    abstract protected function fields(): array;

    abstract protected function create(): array;

    abstract protected function edit(): array;

    /**
     * Returns the model instance associated with the form.
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Initialize the form
     */
    protected function initializeForm()
    {
        if (method_exists($this, 'create') && ! $this->model->exists) {
            $this->formConfig = $this->create();
        } elseif (method_exists($this, 'edit') && $this->model->exists) {
            $this->formConfig = $this->edit();
        } else {
            throw new \RuntimeException('No form configuration method found.');
        }

        // if there's an upload widget in the form add the multipart form attribute
        if ($this->hasUploadWidget()) {
            if (! isset($this->formConfig['attributes'])) {
                $this->formConfig['attributes'] = [];
            }
            $this->formConfig['attributes']['enctype'] = 'multipart/form-data';
        }
    }

    protected function hasUploadWidget(): bool
    {
        foreach ($this->fields as $field) {
            if ($field['widget'] instanceof UploadWidget || $field['widget'] == 'upload') {
                return true;
            }
        }

        return false;
    }

    /**
     * Initialize the fields
     */
    protected function initializeFields(): void
    {
        $definedFields = $this->fields();

        foreach ($definedFields as $name => $fieldConfig) {

            $widget = $this->resolveWidget($fieldConfig, $name);
            $this->fields[$name] = [
                'widget' => $widget,
                'config' => $fieldConfig,
            ];

            // Register assets for this widget
            $this->registerWidgetAssets($widget, $fieldConfig);
        }
    }

    protected function resolveWidget(array $fieldConfig, string $fieldName): WidgetInterface
    {
        // Se widget specificato esplicitamente
        if (isset($fieldConfig['widget'])) {
            // Se è una classe completa
            if (is_string($fieldConfig['widget']) && class_exists($fieldConfig['widget'])) {
                return new $fieldConfig['widget'];
            }
            // Se è un alias (stringa breve, es: 'text', 'select2', ecc.)
            if (is_string($fieldConfig['widget'])) {
                // Usa la factory per risolvere l'alias
                return $this->widgetFactory->make($fieldConfig['widget']);
            }
            // Se è già un oggetto widget
            if ($fieldConfig['widget'] instanceof WidgetInterface) {
                return $fieldConfig['widget'];
            }
            // Se arriva qui, il valore non è valido
            throw new \InvalidArgumentException("Invalid widget definition for field '$fieldName'");
        }

        // Auto-detect dal database schema
        $columnType = $this->schemaInspector->getColumnType($this->model, $fieldName);

        return $this->widgetFactory->make($columnType);
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
            $columnType = 'text';
        }

        switch ($columnType) {
            case 'char':
            case 'varchar':
            case 'text':
                return new Widgets\TextWidget;
            case 'textarea':
                return new Widgets\TextareaWidget;
            case 'boolean':
            case 'tinyint':
                return new Widgets\ToggleWidget;
            case 'date':
                return new Widgets\DateWidget;
            case 'datetime':
            case 'timestamp':
                return new Widgets\DateTimeWidget;
            default:
                return new Widgets\TextWidget;
        }
    }

    public function renderForm()
    {
        return view('formello::form', [
            'formello' => $this,
            'formConfig' => $this->formConfig,
        ])->render();
    }

    public function render()
    {
        return view('formello::form', [
            'formello' => $this,
            'formConfig' => $this->formConfig,
        ])->render();
    }

    public function renderField(string $name): string
    {
        if (! isset($this->fields[$name])) {
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

    public function isCreating(): bool
    {
        return $this->formMode === 'create';
    }

    public function isEditing(): bool
    {
        return $this->formMode === 'edit';
    }

    public function setFormMode(string $mode): self
    {
        $this->formMode = $mode;

        return $this;
    }

    /**
     * Register assets for a widget
     */
    protected function registerWidgetAssets(WidgetInterface $widget, array $fieldConfig): void
    {
        $type = $widget->getWidgetName();
        $assetConfig = config('formello.assets', []);

        // Check if assets are enabled for this widget type
        if (! ($assetConfig[$type] ?? true)) {
            return;
        }

        // Get assets from widget, passing field configuration for conditional assets
        $assets = $widget->getAssets($fieldConfig);

        if ($assets) {
            $this->registerAssets($assets);
        }
    }

    /**
     * Register an array of assets
     */
    protected function registerAssets(array $assets): void
    {
        if (isset($assets['scripts'])) {
            foreach ($assets['scripts'] as $script) {
                AssetManager::addScript($script);
            }
        }

        if (isset($assets['styles'])) {
            foreach ($assets['styles'] as $style) {
                AssetManager::addStyle($style);
            }
        }
    }
}
