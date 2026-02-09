<?php

namespace Metalogico\Formello;

use Metalogico\Formello\Interfaces\WidgetInterface;

class FormelloField
{
    protected string $name;

    protected ?string $label = null;

    protected ?int $columns = null;

    protected mixed $value = null;

    protected bool $value_set = false;

    protected ?string $widget_type = null;

    protected array $widget_options = [];

    protected array|\Closure $choices = [];

    protected bool $multiple = false;

    protected array $attributes = [];

    protected ?string $help = null;

    protected ?string $icon = null;

    protected ?string $type = null;

    protected ?string $format = null;

    protected array $reactive = [];

    protected array $extra = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function columns(int $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function value(mixed $value): static
    {
        $this->value = $value;
        $this->value_set = true;

        return $this;
    }

    public function widget(string $type, array $options = []): static
    {
        $this->widget_type = $type;
        $this->widget_options = $options;

        return $this;
    }

    public function choices(array|\Closure $choices): static
    {
        $this->choices = $choices;

        return $this;
    }

    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function attributes(array $attributes): static
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    public function required(bool $required = true): static
    {
        $this->attributes['required'] = $required;

        return $this;
    }

    public function readonly(bool $readonly = true): static
    {
        $this->attributes['readonly'] = $readonly;

        return $this;
    }

    public function disabled(bool $disabled = true): static
    {
        $this->attributes['disabled'] = $disabled;

        return $this;
    }

    public function help(string $help): static
    {
        $this->help = $help;

        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function reactive(array $config): static
    {
        $this->reactive = $config;

        return $this;
    }

    public function extra(string $key, mixed $value): static
    {
        $this->extra[$key] = $value;

        return $this;
    }

    // ── Getters ──────────────────────────────────────────────

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getColumns(): ?int
    {
        return $this->columns;
    }

    public function getWidgetType(): ?string
    {
        return $this->widget_type;
    }

    public function getWidgetOptions(): array
    {
        return $this->widget_options;
    }

    public function hasValueSet(): bool
    {
        return $this->value_set;
    }

    /**
     * Convert the fluent field definition to the legacy array format
     * consumed by widget getViewData() methods.
     */
    public function toArray(): array
    {
        $config = [];

        if ($this->label !== null) {
            $config['label'] = $this->label;
        }

        if ($this->widget_type !== null) {
            $config['widget'] = $this->widget_type;
        }

        if ($this->columns !== null) {
            $config['columns'] = $this->columns;
        }

        if ($this->value_set) {
            $config['value'] = $this->value;
        }

        if (! empty($this->choices) || $this->choices instanceof \Closure) {
            $config['choices'] = $this->choices;
        }

        if ($this->multiple) {
            $config['multiple'] = true;
        }

        if (! empty($this->attributes)) {
            $config['attributes'] = $this->attributes;
        }

        if ($this->help !== null) {
            $config['help'] = $this->help;
        }

        if ($this->icon !== null) {
            $config['icon'] = $this->icon;
        }

        if ($this->type !== null) {
            $config['type'] = $this->type;
        }

        if ($this->format !== null) {
            $config['format'] = $this->format;
        }

        if (! empty($this->reactive)) {
            $config['reactive'] = $this->reactive;
        }

        // Widget-specific options go under the widget type key
        // e.g. 'tomselect' => [...], 'flatpickr' => [...], 'pickr' => [...]
        if ($this->widget_type !== null && ! empty($this->widget_options)) {
            $config[$this->widget_type] = $this->widget_options;
        }

        // Merge any extra keys
        foreach ($this->extra as $key => $value) {
            $config[$key] = $value;
        }

        return $config;
    }
}
