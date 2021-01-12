<?php

namespace SkyRaptor\Chatter\Tests\Tests;

use SkyRaptor\Chatter\Tests\Tests\Abstracts\TestCase;

class RoutesTest extends TestCase
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

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testForumRoutes()
    {
        $urls = [
            route('chatter.home'),
            route('chatter.discussion.showInCategory', ['category' => 'general', 'discussion' => 'welcome-to-the-chatter-laravel-forum-package']),
            route('chatter.category.show', ['category' => 'introductions']),
        ];

        foreach ($urls as $url) {
            $response = $this->call('GET', $url);
            $this->assertEquals(200, $response->status(), $url.' did not return a 200, it did return: ');
        }
    }
}
