<?php

namespace Metalogico\Formello;

use Metalogico\Formello\Interfaces\WidgetInterface;

class WidgetFactory
{
    private array $widgetMap;
    
    public function __construct()
    {
        $this->widgetMap = config('formello.default_widgets', [
            'text' => Widgets\TextWidget::class,
            'textarea' => Widgets\TextareaWidget::class,
            'boolean' => Widgets\ToggleWidget::class,
            'date' => Widgets\DateWidget::class,
            'datetime' => Widgets\DateTimeWidget::class,
            'select' => Widgets\SelectWidget::class,
            'select2' => Widgets\Select2Widget::class,
            'checkboxes' => Widgets\CheckboxesWidget::class,
            'radio' => Widgets\RadioWidget::class,
            'range' => Widgets\RangeWidget::class,
            'upload' => Widgets\UploadWidget::class,
            'hidden' => Widgets\HiddenWidget::class,
            'color' => Widgets\ColorWidget::class,
            'colorswatch' => Widgets\ColorSwatchWidget::class,
        ]);
    }
    
    public function make(string $type): WidgetInterface
    {
        $widgetClass = $this->widgetMap[$type] ?? Widgets\TextWidget::class;
        
        if (!class_exists($widgetClass)) {
            throw new \InvalidArgumentException("Widget class {$widgetClass} not found");
        }
        
        $widget = new $widgetClass();
        
        if (!$widget instanceof WidgetInterface) {
            throw new \InvalidArgumentException("Widget must implement WidgetInterface");
        }
        
        return $widget;
    }
}