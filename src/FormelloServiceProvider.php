<?php

namespace Metalogico\Formello;

use Metalogico\Formello\Formello;
use Metalogico\Formello\WidgetFactory;
use Illuminate\Support\ServiceProvider;
use Metalogico\Formello\FormelloManager;
use Metalogico\Formello\SchemaInspector;
use Metalogico\Formello\Console\MakeFormelloCommand;

class FormelloServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/formello.php', 'formello');

        $this->app->singleton('formello', FormelloManager::class);
        $this->app->bind(Formello::class, FormelloManager::class);
        
        // Register factory and inspector
        $this->app->singleton(WidgetFactory::class);
        $this->app->singleton(SchemaInspector::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'formello');

        $this->publishes([
            __DIR__ . '/../config/formello.php' => config_path('formello.php'),
        ], 'formello-config');
        
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/formello'),
        ], 'formello-views');

        $this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/formello'),
        ], 'formello-assets');

        if ($this->app->runningInConsole()) {
            $this->commands([MakeFormelloCommand::class]);
        }
    }
}