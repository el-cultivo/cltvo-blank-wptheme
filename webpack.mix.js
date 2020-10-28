const mix = require('laravel-mix');

mix.sourceMaps(true, 'source-map')
	.js( 'es6/micorriza.js', 'js/functions.js')
	.sass( 'sass/mazorca.scss', './style.css')
	.options({
    	processCssUrls: false,
	})
	.browserSync({
		open: 'local',
		host: 'localhost',
		proxy: 'localhost',
		files: ['views/*.php', '*.php', 'js/*.js', '*.css']
	});