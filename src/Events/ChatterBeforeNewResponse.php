<?php

namespace SkyRaptor\Chatter\Events;

use SkyRaptor\Chatter\Models\Discussion;

class ChatterBeforeNewResponse
{
    public Discussion $discussion;

    function __construct(Discussion $discussion)
    {
        $this->discussion = $discussion;
    }
}
