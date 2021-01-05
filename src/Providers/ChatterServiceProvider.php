<?php

namespace SkyRaptor\Chatter\Providers;

use Illuminate\Support\ServiceProvider;

class ChatterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'chatter');

        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');

        $this->publishes([
            __DIR__ . '/../../public' => public_path('vendor/skyraptor/chatter'),
        ], 'chatter_assets');

        $this->publishes([
            __DIR__.'/../../config/chatter.php' => config_path('chatter.php'),
        ], 'chatter_config');

        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('migrations'),
        ], 'chatter_migrations');

        $this->publishes([
            __DIR__.'/../../database/seeds/' => database_path('seeds'),
        ], 'chatter_seeds');

        $this->publishes([
            __DIR__.'/../../resources/sass' => resource_path('sass/vendor/chatter'),
        ], 'chatter_resources');

        $this->publishes([
            __DIR__.'/../../resources/js' => resource_path('js/vendor/chatter'),
        ], 'chatter_resources');

        $this->publishes([
            __DIR__.'/../../resources/lang' => resource_path('lang/vendor/chatter'),
        ], 'chatter_lang');
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
