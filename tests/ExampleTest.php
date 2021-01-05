<?php

namespace SkyRaptor\Chatter\Tests;

class ExampleTest extends \Orchestra\Testbench\TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        /* Test that we can see the basic Laravel page */
        $this->call('GET', '/')->assertSee('Laravel');

        /* Test that we can see the forum frontend */
        $this->call('GET', route('chatter.' . config('chatter.routes.home')))->assertSee('New Discussion');
    }
}
