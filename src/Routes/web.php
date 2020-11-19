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
    Route::get('/', [
        'as'         => 'home',
        'uses'       => [Config::get('chatter.controllers.default'), 'index'],
        'middleware' => $middleware('home'),
    ]);

    // Single category view.
    Route::get($route('category').'/{slug}', [
        'as'         => 'category.show',
        'uses'       => [Config::get('chatter.controllers.default'), 'index'],
        'middleware' => $middleware('category.show'),
    ]);

    /*
     * Discussion routes.
     */
    Route::group([
        'as'     => 'discussion.',
        'prefix' => $route('discussion'),
    ], function () use ($middleware, $authMiddleware) {

        // All discussions view.
        Route::get('/', [
            'as'         => 'index',
            'uses'       => [Config::get('chatter.controllers.discussion'), 'index'],
            'middleware' => $middleware('discussion.index'),
        ]);

        // Create discussion view.
        Route::get('create', [
            'as'         => 'create',
            'uses'       => [Config::get('chatter.controllers.discussion'), 'create'],
            'middleware' => $authMiddleware('discussion.create'),
        ]);

        // Store discussion action.
        Route::post('/', [
            'as'         => 'store',
            'uses'       => [Config::get('chatter.controllers.discussion'), 'store'],
            'middleware' => $authMiddleware('discussion.store'),
        ]);

        // Single discussion view.
        Route::get('{category}/{slug}', [
            'as'         => 'showInCategory',
            'uses'       => [Config::get('chatter.controllers.discussion'), 'show'],
            'middleware' => $middleware('discussion.show'),
        ]);

        // Add user notification to discussion
        Route::post('{category}/{slug}/email', [
            'as'         => 'email',
            'uses'       => [Config::get('chatter.controllers.discussion'), 'toggleEmailNotification'],
        ]);

        /*
         * Specific discussion routes.
         */
        Route::group([
            'prefix' => '{discussion}',
        ], function () use ($middleware, $authMiddleware) {

            // Single discussion view.
            Route::get('/', [
                'as'         => 'show',
                'uses'       => [Config::get('chatter.controllers.discussion'), 'show'],
                'middleware' => $middleware('discussion.show'),
            ]);

            // Edit discussion view.
            Route::get('edit', [
                'as'         => 'edit',
                'uses'       => [Config::get('chatter.controllers.discussion'), 'edit'],
                'middleware' => $authMiddleware('discussion.edit'),
            ]);
            
            // Update discussion action.
            Route::match(['PUT', 'PATCH'], '/', [
                'as'         => 'update',
                'uses'       => [Config::get('chatter.controllers.discussion'), 'update'],
                'middleware' => $authMiddleware('discussion.update'),
            ]);
            
            // Destroy discussion action.
            Route::delete('/', [
                'as'         => 'destroy',
                'uses'       => [Config::get('chatter.controllers.discussion'), 'destroy'],
                'middleware' => $authMiddleware('discussion.destroy'),
            ]);
        });
    });

    /*
     * Post routes.
     */
    Route::group([
        'as'     => 'posts.',
        'prefix' => $route('post', 'posts'),
    ], function () use ($middleware, $authMiddleware) {

        // All posts view.
        Route::get('/', [
            'as'         => 'index',
            'uses'       => [Config::get('chatter.controllers.post'), 'index'],
            'middleware' => $middleware('post.index'),
        ]);

        // Create post view.
        Route::get('create', [
            'as'         => 'create',
            'uses'       => [Config::get('chatter.controllers.post'), 'create'],
            'middleware' => $authMiddleware('post.create'),
        ]);

        // Store post action.
        Route::post('/', [
            'as'         => 'store',
            'uses'       => [Config::get('chatter.controllers.post'), 'store'],
            'middleware' => $authMiddleware('post.store'),
        ]);

        /*
         * Specific post routes.
         */
        Route::group([
            'prefix' => '{post}',
        ], function () use ($middleware, $authMiddleware) {

            // Update post action.
            Route::match(['PUT', 'PATCH'], '/', [
                'as'         => 'update',
                'uses'       => [Config::get('chatter.controllers.post'), 'update'],
                'middleware' => $authMiddleware('post.update'),
            ]);

            // Destroy post action.
            Route::delete('/', [
                'as'         => 'destroy',
                'uses'       => [Config::get('chatter.controllers.post'), 'destroy'],
                'middleware' => $authMiddleware('post.destroy'),
            ]);
        });
    });
});

/*
 * Atom routes
 */
Route::get($route('home').'.atom', [
    'as'         => 'chatter.atom',
    'uses'       => [Config::get('chatter.controllers.atom'), 'index'],
    'middleware' => $middleware('home'),
]);
