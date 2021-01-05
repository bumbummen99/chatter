<?php

namespace SkyRaptor\Chatter\Tests;

class RoutesTest extends \Orchestra\Testbench\TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testForumRoutes()
    {
        $urls = [
            '/forums',
            '/forums/discussion/general/welcome-to-the-chatter-laravel-forum-package',
            '/forums/category/introductions',
        ];

        foreach ($urls as $url) {
            $response = $this->call('GET', $url);
            $this->assertEquals(200, $response->status(), $url.' did not return a 200');
        }
    }
}
