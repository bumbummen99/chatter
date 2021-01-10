<?php

namespace SkyRaptor\Chatter\Events;

use SkyRaptor\Chatter\Models\Category;

class ChatterCategorySaved
{
    public Category $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }
}
