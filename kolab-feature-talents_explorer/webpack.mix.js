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
    .sass('resources/sass/app.scss', 'public/css');

mix.copy('node_modules/blueimp-file-upload/js/jquery.fileupload.js', 'public/js')
  .copy('node_modules/blueimp-file-upload/js/jquery.iframe-transport.js', 'public/js')
  .copy('node_modules/blueimp-file-upload/js/jquery.fileupload-process.js', 'public/js')
  .copy('node_modules/blueimp-file-upload/js/jquery.fileupload-validate.js', 'public/js')
  .copy('node_modules/blueimp-file-upload/js/vendor/jquery.ui.widget.js', 'public/js/vendor');

// Copy of scripts and images
mix.copyDirectory('resources/images', 'public/images');
mix.copyDirectory('resources/js/plugins/', 'public/js')
mix.copyDirectory('resources/fonts/', 'public/fonts')
.options({
	processCssUrls: false
})
.sourceMaps()
.webpackConfig({
	devtool: 'source-map',
});

mix.options({
    hmrOptions: {
        host: 'kolab.test',  // mysite.test is my local domain used for testing
        port: 8080,
    }
});
