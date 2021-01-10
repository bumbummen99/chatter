<?php

namespace SkyRaptor\Chatter\Events;

use SkyRaptor\Chatter\Models\Discussion;
use SkyRaptor\Chatter\Models\Post;

class ChatterAfterNewResponse
{
    public Discussion $discussion;

    public Post $post;

    public function __construct(Discussion $discussion, Post $post)
    {
        $this->discussion = $discussion;

        $this->post = $post;
    }
}
