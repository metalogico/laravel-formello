<?php

namespace Metalogico\Formello;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ViewErrorBag;
use Metalogico\Formello\Interfaces\WidgetInterface;
use Metalogico\Formello\Widgets\UploadWidget;
use InvalidArgumentException;

abstract class Formello
{
    protected string $formMode;

    protected Model $model;

    protected ViewErrorBag $errors;

    protected array $formConfig = [];

    protected array $fields = [];

    private WidgetFactory $widgetFactory;

    private SchemaInspector $schemaInspector;

    /**
     * Track whether we have registered widget assets for this form instance.
     * Assets depend on the resolved CSS framework, so we defer registration
     * until render time (after potential setCssFramework() calls).
     */
    protected bool $assetsRegistered = false;

    /**
     * Optional per-form CSS framework override.
     * If null, falls back to the global config('formello.css_framework').
     */
    protected ?string $cssFramework = null;

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
        // If a class-string is given, instantiate a new model
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

        $this->initializeForm();
        $this->initializeFields();
        $this->ensureMultipartIfNeeded();
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
        $this->formConfig = $this->model->exists
            ? $this->edit()
            : $this->create();
    }

    protected function hasUploadWidget(): bool
    {
        foreach ($this->fields as $field) {
            if ($field['widget'] instanceof UploadWidget) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ensure the form has the correct enctype if an upload widget is present
     */
    protected function ensureMultipartIfNeeded(): void
    {
        if ($this->hasUploadWidget()) {
            if (! isset($this->formConfig['attributes'])) {
                $this->formConfig['attributes'] = [];
            }
            $this->formConfig['attributes']['enctype'] = 'multipart/form-data';
        }
    }

    /**
     * Initialize the fields
     */
    protected function initializeFields(): void
    {
        $defined_fields = $this->fields();

        foreach ($defined_fields as $field) {
            if (! $field instanceof FormelloField) {
                throw new InvalidArgumentException(
                    'fields() must return an array of FormelloField instances.'
                );
            }

            $name = $field->getName();
            $field_config = $field->toArray();

            // Auto-resolve value from model when not explicitly set
            if (! $field->hasValueSet() && isset($this->model->{$name})) {
                $field_config['value'] = $this->model->{$name};
            }

            $widget = $this->resolveWidget($field_config, $name);
            $this->fields[$name] = [
                'field' => $field,
                'widget' => $widget,
                'config' => $field_config,
            ];
        }
    }

    /**
     * Ensure assets are registered once, using the current CSS framework value.
     */
    protected function ensureAssetsRegistered(): void
    {
        if ($this->assetsRegistered) {
            return;
        }

        // Bind current form instance so widgets can resolve per-form settings
        app()->instance('formello', $this);

        foreach ($this->fields as $name => $field) {
            $this->registerWidgetAssets($field['widget'], $field['config']);
        }

        $this->assetsRegistered = true;
    }

    protected function resolveWidget(array $field_config, string $field_name): WidgetInterface
    {
        if (isset($field_config['widget'])) {
            if (is_string($field_config['widget'])) {
                return $this->widgetFactory->make($field_config['widget']);
            }
            throw new InvalidArgumentException("Invalid widget definition for field '$field_name'");
        }

        // Auto-detect from database schema
        $column_type = $this->schemaInspector->getColumnType($this->model, $field_name);

        return $this->widgetFactory->make($column_type);
    }

    public function render()
    {
        // Register assets now, so per-form CSS framework overrides are applied
        $this->ensureAssetsRegistered();

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

        // Ensure widgets resolve the current form instance when calling app('formello')
        app()->instance('formello', $this);

        // Ensure assets are registered (important when rendering single fields)
        $this->ensureAssetsRegistered();

        return $widget->render($name, $value, $config, $errors);
    }

    public function getCssFramework()
    {
        return $this->cssFramework
            ?? ($this->formConfig['css_framework'] ?? null)
            ?? config('formello.css_framework', 'bootstrap5');
    }

    /**
     * Override the CSS framework for this form instance.
     */
    public function setCssFramework(string $framework): self
    {
        $this->cssFramework = $framework;

        return $this;
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
