<?php

namespace Metalogico\Formello;

use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Metalogico\Formello\Widgets\UploadWidget;
use Metalogico\Formello\Interfaces\WidgetInterface;

abstract class Formello
{
    public string $formMode;

    protected Model $model;

    protected ViewErrorBag $errors;

    protected array $formConfig = [];

    protected array $fields = [];

    /**
     * Creates a new instance of Formello.
     *
     * @param  Model|string  $model  Model instance or class-string
     * @param  ViewErrorBag|null  $errors  Validation errors bag
     */
    public function __construct(
        Model|string $model,
        ?ViewErrorBag $errors = null,
    ) {
        // Se è una stringa, creiamo una nuova istanza del modello
        if (is_string($model)) {
            $this->model = new $model;
        } else {
            $this->model = $model;
        }
        $this->errors = $errors ?? session()->get('errors', new ViewErrorBag);

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

        SchemaInspector::assignDefaultWidgets($this->model, $definedFields);

        foreach ($definedFields as $field) {
            $widget = $field->getWidget();
            $this->fields[$field->name] = [
                'widget' => $widget,
                'field' => $field,
            ];

            // Register assets for this widget
            $this->registerWidgetAssets($widget);
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

        $fieldData = $this->fields[$name];
        $field = $fieldData['field'];
        $widget = $fieldData['widget'];

        $value = old($name, $field->getValue() ?? $this->model->{$name} ?? null);
        $errors = $this->errors->get($name);

        return $widget->render($field, $value, $errors);
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
    protected function registerWidgetAssets(WidgetInterface $widget): void
    {
        $type = $widget->getWidgetName();
        $assetConfig = config('formello.assets', []);

        // Check if assets are enabled for this widget type
        if (! ($assetConfig[$type] ?? true)) {
            return;
        }

        // Get assets from widget, passing field configuration for conditional assets
        $assets = $widget->getAssets();

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
