<?php

namespace SkyRaptor\Chatter\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use SkyRaptor\Chatter\Events\ChatterCategorySaved;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ChatterCategorySaved::class => [
            UpdatePostTitle::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}