<?php

namespace SkyRaptor\Chatter;

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
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'chatter');

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/skyraptor/chatter'),
        ], 'chatter_assets');

        $this->publishes([
            __DIR__.'/../config/chatter.php' => config_path('chatter.php'),
        ], 'chatter_config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'chatter_migrations');

        $this->publishes([
            __DIR__.'/../database/seeds/' => database_path('seeds'),
        ], 'chatter_seeds');

        $this->publishes([
            __DIR__.'/../resources/sass' => resource_path('sass/vendor/chatter'),
        ], 'chatter_resources');

        $this->publishes([
            __DIR__.'/../resources/js' => resource_path('js/vendor/chatter'),
        ], 'chatter_resources');

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/chatter'),
        ], 'chatter_lang');

        // include the routes file
        include __DIR__ . '/Routes/web.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        /*
         * Register the service provider for the dependency.
         */
        $this->app->register(\Mews\Purifier\PurifierServiceProvider::class);

        /*
         * Create aliases for the dependency.
         */
        $this->app->alias('Mews\Purifier\Facades\Purifier', 'Purifier');

        /*
         * Load view files.
         */
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'chatter');
    }
}
