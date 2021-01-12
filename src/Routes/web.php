<?php

/**
 * Helpers.
 */

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

// Route helper.
$route = function ($accessor, $default = '') {
    return Config::get('chatter.routes.' . $accessor, $default);
};

// Middleware helper.
$middleware = function ($accessor, $default = []) {
    return Config::get('chatter.middleware.' . $accessor, $default);
};

// Authentication middleware helper.
$authMiddleware = function ($accessor) use ($middleware) {
    return array_unique(
        array_merge((array) $middleware($accessor), ['auth'])
    );
};

/*
 * Chatter routes.
 */
Route::group([
    'as'         => 'chatter.',
    'prefix'     => $route('home'),
    'middleware' => $middleware('global', 'web'),
], function () use ($route, $middleware, $authMiddleware) {

    // Home view.
    Route::middleware($middleware('home'))
    ->get('/', [Config::get('chatter.controllers.default'), 'index'])
    ->name('home');

    // Single category view.
    Route::middleware($middleware('category.show'))
    ->get($route('category').'/{category:slug}', [Config::get('chatter.controllers.default'), 'index'])
    ->name('category.show');

    /*
     * Discussion routes.
     */
    Route::group([
        'as'     => 'discussion.',
        'prefix' => $route('discussion'),
    ], function () use ($middleware, $authMiddleware) {
        // Store discussion action.
        Route::middleware($authMiddleware('discussion.store'))
        ->post('/', [Config::get('chatter.controllers.discussion'), 'store'])
        ->name('store');

        // Single discussion view.
        Route::middleware($middleware('discussion.show'))
        ->get('{category:slug}/{discussion:slug}', [Config::get('chatter.controllers.discussion'), 'show'])
        ->name('showInCategory');

        /*
         * Specific discussion routes.
         */
        Route::group([
            'prefix' => '{discussion}',
        ], function () use ($middleware, $authMiddleware) {            
            // Update discussion action.
            Route::middleware($authMiddleware('discussion.update'))
            ->match(['PUT', 'PATCH'], '/', [Config::get('chatter.controllers.discussion'), 'update'])
            ->name('update');
        });
    });

    /*
     * Post routes.
     */
    Route::group([
        'as'     => 'posts.',
        'prefix' => $route('post', 'posts'),
    ], function () use ($middleware, $authMiddleware) {
        // Store post action.
        Route::middleware($authMiddleware('post.store'))
        ->post('/', [Config::get('chatter.controllers.post'), 'store'])
        ->name('store');

        /*
         * Specific post routes.
         */
        Route::group([
            'prefix' => '{post}',
        ], function () use ($middleware, $authMiddleware) {

            // Update post action.
            Route::middleware($authMiddleware('post.update'))
            ->match(['PUT', 'PATCH'], '/', [Config::get('chatter.controllers.post'), 'update'])
            ->name('update');

            // Destroy post action.
            Route::middleware($authMiddleware('post.destroy'))
            ->delete('/', [Config::get('chatter.controllers.post'), 'destroy'])
            ->name('destroy');
        });
    });
});

/*
 * Atom routes
 */
Route::middleware($authMiddleware('home'))
->get($route('home').'.atom', [Config::get('chatter.controllers.atom'), 'index'])
->name('chatter.atom');
