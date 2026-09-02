<?php

namespace Metalogico\Formello;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Metalogico\Formello\Console\MakeFormelloCommand;
use Metalogico\Formello\Http\Controllers\FormelloComputeController;

class FormelloServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/formello.php', 'formello');

        $this->app->singleton('formello', FormelloManager::class);

        // Register factory and inspector
        $this->app->singleton(WidgetFactory::class);
        $this->app->singleton(SchemaInspector::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'formello');

        $this->publishes([
            __DIR__.'/../config/formello.php' => config_path('formello.php'),
        ], 'formello-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/formello'),
        ], 'formello-views');

        $this->publishes([
            // Main script
            __DIR__.'/../resources/assets/js/formello.js' => public_path('vendor/formello/js/formello.js'),
            // IMask
            __DIR__.'/../resources/assets/js/imask.min.js' => public_path('vendor/formello/js/imask.min.js'),
            // Flatpickr
            __DIR__.'/../resources/assets/js/flatpickr.min.js' => public_path('vendor/formello/js/flatpickr.min.js'),
            __DIR__.'/../resources/assets/css/flatpickr.min.css' => public_path('vendor/formello/css/flatpickr.min.css'),
            __DIR__.'/../resources/assets/js/l10n/it.js' => public_path('vendor/formello/js/l10n/it.js'),
            // Pickr
            __DIR__.'/../resources/assets/js/pickr.min.js' => public_path('vendor/formello/js/pickr.min.js'),
            __DIR__.'/../resources/assets/css/nano.min.css' => public_path('vendor/formello/css/nano.min.css'),
            // Jodit
            __DIR__.'/../resources/assets/js/jodit.min.js' => public_path('vendor/formello/js/jodit.min.js'),
            __DIR__.'/../resources/assets/css/jodit.min.css' => public_path('vendor/formello/css/jodit.min.css'),
            // Tom Select
            __DIR__.'/../resources/assets/js/tom-select.complete.js' => public_path('vendor/formello/js/tom-select.complete.js'),
            __DIR__.'/../resources/assets/css/tom-select.default.min.css' => public_path('vendor/formello/css/tom-select.default.min.css'),
            // Reactive engine
            __DIR__.'/../resources/assets/js/formello-reactive.js' => public_path('vendor/formello/js/formello-reactive.js'),
        ], 'formello-assets');

        if ($this->app->runningInConsole()) {
            $this->commands([MakeFormelloCommand::class]);
        }

        $this->registerBladeDirectives();
        $this->registerReactiveRoute();
    }

    /**
     * Register Blade directives for Formello assets
     */
    protected function registerBladeDirectives(): void
    {
        Blade::directive('formelloStyles', function () {
            return "<?php echo view('formello::directives.styles')->render(); ?>";
        });

        Blade::directive('formelloScripts', function () {
            return "<?php echo view('formello::directives.scripts')->render(); ?>";
        });
    }

    /**
     * Register the POST route for reactive server callbacks.
     */
    protected function registerReactiveRoute(): void
    {
        $compute_path = config('formello.reactive.compute_path');

        if ($compute_path) {
            $middleware = config('formello.reactive.middleware', ['web', 'auth']);

            Route::post($compute_path, [FormelloComputeController::class, 'handle'])
                ->name('formello.compute')
                ->middleware($middleware);
        }
    }
}
