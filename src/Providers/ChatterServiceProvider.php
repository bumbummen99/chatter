<?php

namespace SkyRaptor\Chatter\Providers;

use Illuminate\Support\ServiceProvider;

class ChatterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'chatter');

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');

        $this->publishes([
            __DIR__ . '/../../config/chatter.php' => config_path('chatter.php'),
        ], 'chatter-config');

        $this->publishes([
            __DIR__ . '/../../public' => public_path('vendor/skyraptor/chatter'),
        ], 'chatter-assets');

        $this->publishes([
            __DIR__ . '/../../resources/sass' => resource_path('sass/vendor/chatter'),
        ], 'chatter-resources');

        $this->publishes([
            __DIR__ . '/../../resources/js' => resource_path('js/vendor/chatter'),
        ], 'chatter-resources');

        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/chatter'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        /*
         * Load view files.
         */
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'chatter');
    }
}
