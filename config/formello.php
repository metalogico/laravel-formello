<?php

return [

    /**
     * Options bootstrap5 tailwindcss4
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
    /**
     * Reactive system configuration
     */
    'reactive' => [
        'compute_path' => '/formello/compute',

        // Middleware applied to the compute route. Include 'web' for session/CSRF.
        'middleware' => ['web', 'auth'],

        // Whitelist of form classes allowed for server callbacks.
        // Empty = reject all (fail-closed). Use ['*'] only for local development.
        'allowed_forms' => [
            // App\Forms\ContractForm::class,
        ],

        // Optional: fn (\Illuminate\Http\Request $request, mixed $model): bool
        // Return false to deny loading/using the model (403).
        'authorize_model' => null,
    ],

    'assets' => [
        'tomselect' => true,
        'date' => true,
        'datetime' => true,
        'mask' => true,
        'color' => true,
        'colorswatch' => true,
        'wysiwyg' => true,
    ],

];
