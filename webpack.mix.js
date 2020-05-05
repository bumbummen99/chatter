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

mix.js('src/resources/js/chatter-discussion.js', 'public/assets/js')
.js('src/resources/js/chatter-home.js', 'public/assets/js')
.sass('src/resources/sass/chatter.scss', 'public/assets/css')
.sass('src/resources/sass/tinymce.scss', 'public/assets/css');