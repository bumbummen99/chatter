<?php

namespace SkyRaptor\Chatter\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use SkyRaptor\Chatter\Models\Post;

trait ChatterUserTrait {
    /**
     * Get the Users forum name.
     */
    public function getForumName() : string
    {
        return $this->name;
    }

    /**
     * Relation for the Users forum Discussions.
     */
    public function discussions() : HasMany
    {
        return $this->hasMany('App\Discussion');
    }

    /**
     * Relation for the Users forum Posts.
     */
    public function posts() : HasMany
    {
        return $this->hasMany(Post::class);
    }
}