<?php

namespace SkyRaptor\Chatter\Events;

use Illuminate\Http\Request;

class ChatterCategorySaved
{
    /**
     * @var Model::category()
     */
    public $category;

    /**
     * Constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request, $category)
    {
        $this->request = $request;

        $this->category = $category;
    }
}
