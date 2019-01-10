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

/*
mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css');
*/

//Assets in auth
mix.copy('resources/assets/template/css/vendors.bundle.css',
    'public/assets/css/vendors.bundle.css');
mix.copy('resources/assets/template/css/style.bundle.css',
    'public/assets/css/style.bundle.css');
mix.copy('resources/assets/template/css/fullcalendar.bundle.css',
    'public/assets/css/fullcalendar.bundle.css');

mix.copy('resources/assets/template/js/vendors.bundle.js',
    'public/assets/js/vendors.bundle.js');
mix.copy('resources/assets/template/js/scripts.bundle.js',
    'public/assets/js/scripts.bundle.js');
mix.copy('resources/assets/template/js/login.js',
    'public/assets/auth/js/login.js');
mix.copy('resources/assets/template/js/dashboard.js',
    'public/assets/js/dashboard.js');
mix.copy('resources/assets/template/js/fullcalendar.bundle.js',
    'public/assets/js/fullcalendar.bundle.js');
mix.copy('resources/assets/template/js/summernote.js',
    'public/assets/js/summernote.js');
mix.copy('resources/assets/template/js/select2.js',
    'public/assets/js/select2.js');
mix.copy('resources/assets/template/js/treeview.js',
    'public/assets/js/treeview.js');
mix.copy('resources/assets/js/document.js',
    'public/assets/js/document.js');
mix.copy('resources/assets/template/js/select.js',
    'public/assets/js/select.js');
mix.copy('resources/assets/template/js/datepicker.js',
    'public/assets/js/datepicker.js');
mix.copy('resources/assets/js/ajaxDocument.js',
    'public/assets/js/ajaxDocument.js');

mix.copy('resources/assets/template/img/favicon.ico',
    'public/assets/img/favicon.ico');
mix.copy('resources/assets/template/img/bg-1.jpg',
    'public/assets/auth/img/bg-1.jpg');
mix.copy('resources/assets/template/img/logo-1.png',
    'public/assets/auth/img/logo-1.png');
mix.copy('resources/assets/template/img/32px.png',
    'public/assets/css/images/jstree/32px.png');
mix.copy('resources/assets/template/img/40px.png',
    'public/assets/css/images/jstree/40px.png');
mix.copy('resources/assets/template/img/throbber.gif',
    'public/assets/css/images/jstree/throbber.gif');
mix.copy('resources/assets/template/img/default.jpg',
    'public/assets/auth/img/default.jpg');

mix.copy('resources/assets/template/fonts/',
    'public/assets/css/fonts/');
