<?php

namespace Metalogico\Formello;

use Metalogico\Formello\Interfaces\WidgetInterface;

class WidgetFactory
{
    private array $widgetMap;

    public function __construct()
    {
        // Built-in widget aliases
        $this->widgetMap = [
            'text' => Widgets\TextWidget::class,
            'email' => Widgets\TextWidget::class,
            'password' => Widgets\TextWidget::class,
            'textarea' => Widgets\TextareaWidget::class,
            'toggle' => Widgets\ToggleWidget::class,
            'date' => Widgets\DateWidget::class,
            'datetime' => Widgets\DateTimeWidget::class,
            'timestamp' => Widgets\DateTimeWidget::class,
            'select' => Widgets\SelectWidget::class,
            'tomselect' => Widgets\TomSelectWidget::class,
            'checkboxes' => Widgets\CheckboxesWidget::class,
            'radio' => Widgets\RadioWidget::class,
            'range' => Widgets\RangeWidget::class,
            'upload' => Widgets\UploadWidget::class,
            'hidden' => Widgets\HiddenWidget::class,
            'color' => Widgets\ColorWidget::class,
            'colorswatch' => Widgets\ColorSwatchWidget::class,
            'wysiwyg' => Widgets\WysiwygWidget::class,
            'mask' => Widgets\MaskWidget::class,
            'separator' => Widgets\SeparatorWidget::class,
        ];

        // Merge custom widgets from user config (overrides built-ins)
        $custom = config('formello.custom_widgets', []);
        if (is_array($custom) && ! empty($custom)) {
            $this->widgetMap = array_merge($this->widgetMap, $custom);
        }
    }

    public function make(string $type): WidgetInterface
    {
        $widgetClass = $this->widgetMap[$type] ?? Widgets\TextWidget::class;

        if (! class_exists($widgetClass)) {
            throw new \InvalidArgumentException("Widget class {$widgetClass} not found");
        }

        $widget = new $widgetClass;

        if (! $widget instanceof WidgetInterface) {
            throw new \InvalidArgumentException('Widget must implement WidgetInterface');
        }

        return $widget;
    }
}
