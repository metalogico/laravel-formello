<?php

namespace Metalogico\Formello;

use Illuminate\Database\Eloquent\Model;
use Metalogico\Formello\Interfaces\WidgetInterface;
use Metalogico\Formello\SchemaInspector;
use Metalogico\Formello\WidgetFactory;

class FormelloField
{
    public string $name;
    private ?string $label;
    private ?string $help;
    private bool $required;
    private $value;
    private int $columns;
    private ?WidgetInterface $widget;

    private WidgetFactory $widgetFactory;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->label = null;
        $this->help = null;
        $this->required = false;
        $this->value = null;
        $this->columns = 12;
        $this->widget = null;
        $this->widgetFactory = new WidgetFactory();
    }

    public static function make(string $name)
    {
        return new self($name);
    }

    public function label(string $value): self
    {
        $this->label = $value;

        return $this;
    }
    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function required(bool $value = true): self
    {
        $this->required = $value;

        return $this;
    }
    public function isRequired(): bool
    {
        return $this->required;
    }

    public function value($value): self
    {
        $this->value = $value;

        return $this;
    }
    public function getValue()
    {
        return $this->value;
    }

    public function columns(int $value): self
    {
        $this->columns = $value;

        return $this;
    }
    public function getColumns(): int
    {
        return $this->columns;
    }

    public function help(string $value): self
    {
        $this->help = $value;

        return $this;
    }
    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function widget(string $widgetType, array $widgetConfig = []): self
    {
        $this->widget = $this->widgetFactory->make($widgetType, $widgetConfig);
        return $this;
    }

    public function hasWidget(): bool
    {
        return $this->widget !== null;
    }

    public function getWidget(): ?WidgetInterface
    {
        return $this->widget;

    }

    // TODO: Refactor, this is for backwards compatibility
    public function getConfig(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'required' => $this->required,
            'value' => $this->value,
            'columns' => $this->columns,
            'widget' => $this->widget ? $this->widget->getWidgetName() : null,
        ];
    }
}
