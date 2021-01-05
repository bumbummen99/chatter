<?php

namespace SkyRaptor\Chatter\Tests\Abstracts;

use SkyRaptor\Chatter\EventServiceProvider;
use SkyRaptor\Chatter\Providers\ChatterServiceProvider;
use SkyRaptor\Chatter\Seeders\ChatterTableSeeder;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        /* Seed the Database */
        $this->artisan('db:seed', ['--class' => ChatterTableSeeder::class]);
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function getPackageProviders($app) {
        return [
            ChatterServiceProvider::class,
            EventServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            //'Acme' => 'Acme\Facade'
        ];
    }
}