<?php

namespace Metalogico\Formello;

use Metalogico\Formello\Interfaces\WidgetInterface;

class WidgetFactory
{
    private array $widgetMap;
    
    public function __construct()
    {
        $this->widgetMap = config('formello.default_widgets', [
            'string' => Widgets\TextWidget::class,
            'text' => Widgets\TextareaWidget::class,
            'boolean' => Widgets\ToggleWidget::class,
            'date' => Widgets\DateWidget::class,
            'datetime' => Widgets\DateTimeWidget::class,
            'select' => Widgets\SelectWidget::class,
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