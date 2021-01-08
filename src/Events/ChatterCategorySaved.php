<?php

namespace SkyRaptor\Chatter\Events;

class ChatterCategorySaved
{
    /**
     * @var Model::category()
     */
    public $category;

    public function __construct($category)
    {
        $this->category = $category;
    }
}
