<?php

namespace SkyRaptor\Chatter\Tests\Tests;

use SkyRaptor\Chatter\Tests\Tests\Abstracts\TestCase;

class GenericTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function test_it_can_view_forum()
    {        
        /* Test that we can see the forum frontend */
        $this->get(route('chatter.home'))->assertSee('New Discussion');
    }
}
