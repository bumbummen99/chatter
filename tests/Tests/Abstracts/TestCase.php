<?php

namespace SkyRaptor\Chatter\Tests\Tests\Abstracts;

use GrahamCampbell\TestBench\AbstractPackageTestCase;
use Illuminate\Foundation\Auth\User;
use SkyRaptor\Chatter\Providers\EventServiceProvider;
use SkyRaptor\Chatter\Providers\ChatterServiceProvider;
use SkyRaptor\Chatter\Seeders\ChatterTableSeeder;
use SkyRaptor\Chatter\Tests\Providers\ChatterTestRouteServiceProvider;

class TestCase extends AbstractPackageTestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        /* Load default Laravel migrations */
        $this->loadLaravelMigrations();

        /* Load package migations (should be handled by provider) */
        $this->loadMigrationsFrom(__DIR__ . '/../../../database/migrations');

        /* Seed the Database */
        $this->seed(ChatterTableSeeder::class);

        $this->app->instance('path.public', __DIR__ . '/../../../public');
    }

    /**
     * Setup the application environment.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        /* Load Chatter configuration */
        $app->config->set('chatter', include __DIR__ . '/../../../config/chatter.php');

        $app->config->set('chatter.user.namespace', User::class);

        /* Load stub views */
        $app->config->set('view.paths', array_merge(
            $app->config->get('view.paths', []), [
                __DIR__ . '/../../views'
            ]
        ));
    }

    /**
     * Get the service provider class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return string
     */
    protected function getServiceProviderClass($app)
    {
        return ChatterServiceProvider::class;
    }

    protected function getPackageProviders($app) {
        return [
            ChatterServiceProvider::class,
            EventServiceProvider::class,
            ChatterTestRouteServiceProvider::class,
        ];
    }
}