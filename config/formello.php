<?php

return [

    /**
     * Options bootstrap5 tailwindcss4
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
    /**
     * Reactive system configuration
     */
    'reactive' => [
        'compute_path' => '/formello/compute',
        // Whitelist of form classes allowed for server callbacks.
        // Leave empty to allow all (not recommended in production).
        'allowed_forms' => [
            // App\Forms\ContractForm::class,
        ],
    ],

    'assets' => [
        'select2' => false, // deprecated
        'tomselect' => true,
        'date' => true,
        'datetime' => true,
        'mask' => true,
        'color' => true,
        'colorswatch' => true,
        'wysiwyg' => true,
    ],

];
