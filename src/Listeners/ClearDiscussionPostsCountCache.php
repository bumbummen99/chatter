<?php

namespace SkyRaptor\Chatter\Listeners;

use Illuminate\Support\Facades\Cache;
use SkyRaptor\Chatter\Events\ChatterAfterNewResponse;

class ClearDiscussionPostsCountCache
{
    public function handle(ChatterAfterNewResponse $event)
    {
        /* Get the associated Discussion */
        $discussion = $event->post->discussion;

        /* Delete the posts_count attribute cache */
        Cache::tags(['chatter-discussions', 'chatter-discussion-' . $discussion->id])->forget('chatter-discussion-post-count-' . $discussion->id);
    }
}