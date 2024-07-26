<?php

namespace Metalogico\Formello;

use Illuminate\Support\ServiceProvider;
use Metalogico\Formello\Console\MakeFormelloCommand;

class FormelloServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('formello', function ($app) {
            return new Formello();
        });
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
