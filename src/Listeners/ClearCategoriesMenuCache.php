<?php

namespace SkyRaptor\Chatter\Listeners;

use Illuminate\Support\Facades\Cache;
use SkyRaptor\Chatter\Events\ChatterCategorySaved;

class ClearCategoriesMenuCache
{
    public function handle(ChatterCategorySaved $event)
    {
        Cache::tags(['chatter-categories'])->flush();
    }
}