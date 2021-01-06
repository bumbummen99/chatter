<?php

namespace SkyRaptor\Chatter\Tests\Providers;

use Illuminate\Support\ServiceProvider;

class ChatterTestRouteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}
