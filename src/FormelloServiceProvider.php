<?php

namespace Metalogico\Formello;

use Metalogico\Formello\Formello;
use Illuminate\Support\ServiceProvider;
use Metalogico\Formello\Console\MakeFormelloCommand;

class FormelloServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('formello', function ($app) {
            return new FormelloManager();
        });

        $this->app->bind(Formello::class, FormelloManager::class);
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'formello');

        $this->publishes([
            __DIR__ . '/../config/formello.php' => config_path('formello.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeFormelloCommand::class,
            ]);
        }
    }
}
