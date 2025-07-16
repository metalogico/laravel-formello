<?php

return [

    /**
     * For now, only bootstrap 5 is supported
     */
    'css_framework' => 'bootstrap5',

    /**
     * Customize the CSS classes of the various widgets
     */
    'css_overrides' => [
        'help_text' => 'form-text',
        'labels' => 'form-label',
        'errors' => 'invalid-feedback',
    ],

    /**
     * Override the default widgets
     */
    'default_widgets' => [
        'string' => Metalogico\Formello\Widgets\TextWidget::class,
        'text' => Metalogico\Formello\Widgets\TextareaWidget::class,
        'boolean' => Metalogico\Formello\Widgets\ToggleWidget::class,
        'tinyint' => Metalogico\Formello\Widgets\ToggleWidget::class,        
        'date' => Metalogico\Formello\Widgets\DateWidget::class,
        'datetime' => Metalogico\Formello\Widgets\DateTimeWidget::class,
        'timestamp' => Metalogico\Formello\Widgets\DateTimeWidget::class,
        'select' => Metalogico\Formello\Widgets\SelectWidget::class,
        'checkboxes' => Metalogico\Formello\Widgets\CheckboxesWidget::class,
        'radio' => Metalogico\Formello\Widgets\RadioWidget::class,
        'range' => Metalogico\Formello\Widgets\RangeWidget::class,
        'upload' => Metalogico\Formello\Widgets\UploadWidget::class,
        'hidden' => Metalogico\Formello\Widgets\HiddenWidget::class,
        'select2' => Metalogico\Formello\Widgets\Select2Widget::class,
    ],

];
