<?php

namespace SkyRaptor\Chatter\Interfaces;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface ChatterUser
{
    /**
     * Get the Users forum name.
     */
    public function getForumName() : string;

    /**
     * Relation for the Users forum Discussions.
     */
    public function discussions() : HasMany;

    /**
     * Relation for the Users forum Posts.
     */
    public function posts() : HasMany;
}