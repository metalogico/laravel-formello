<?php

return [

    /**
     * For now, only bootstrap 5 is supported
     */
    'css_framework' => 'bootstrap5',

    /**
     * Custom widgets registered by the application.
     * Map alias => Fully Qualified Class Name. These override built-ins.
     */
    'custom_widgets' => [
        // 'alias' => App\Formello\MyCustomWidget::class,
    ],

    /**
     * Asset loading configuration
     * Set to false any library you already have in your theme to avoid conflicts
     */
    'assets' => [
        'select2' => true,
        'date' => true,
        'datetime' => true,
        'mask' => true,
        'color' => true,
        'colorswatch' => true,
        'wysiwyg' => true,
    ],

];
