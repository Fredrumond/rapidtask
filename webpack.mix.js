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

 mix.js('resources/js/app.js', 'public/js')
 .js('resources/js/tarefas/tarefa-main.js', 'public/js')
 .js('resources/js/times/time-main.js', 'public/js')
 .js('resources/js/times/time-ver.js', 'public/js')
 .js('resources/js/clientes/cliente-main.js', 'public/js')
 .js('resources/js/clientes/cliente-ver.js', 'public/js')
 .js('resources/js/projetos/projeto-main.js', 'public/js')
 .js('resources/js/projetos/projeto-ver.js', 'public/js')
 .js('resources/js/projetos/projeto-detalhes.js', 'public/js')
 .sass('resources/sass/app.scss', 'public/css')
 .sass('resources/sass/global.scss', 'public/css')
 .sass('resources/sass/site/login.scss', 'public/site/css')
 .sass('resources/sass/site/reset.scss', 'public/site/css')
 .sass('resources/sass/backend/perfil.scss', 'public/backend/css')
 .sass('resources/sass/main.scss', 'public/css');

 mix.copyDirectory('resources/images/site', 'public/site/images');
