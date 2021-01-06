const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.setPublicPath('./public/vendor/skyraptor/chatter');

mix
.js('./resources/js/chatter-discussion.js', 'public/vendor/skyraptor/chatter/js')
.js('./resources/js/chatter-home.js', 'public/vendor/skyraptor/chatter/js')
.sass('./resources/sass/chatter.scss', 'public/vendor/skyraptor/chatter/css')
.version();