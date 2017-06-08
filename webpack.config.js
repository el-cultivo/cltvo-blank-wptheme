module.exports = function(main_js_path, dist_js_path) {
	return {
		entry: {
		  functions: main_js_path + 'micorriza.js',
		  'admin-functions': main_js_path + 'micorriza-admin.js',
		},
		module: {
		  loaders: [
		    {
		      test: /\.js$/,
		      exclude: /(node_modules|bower_components)/,
		      loader: 'babel',
		      query: {
		        presets: ['es2015']
		      }
		    }
		  ]
		},
		output: {
			path: './'+ dist_js_path +'/',
			publicPath: './'+ dist_js_path +'/',
			filename: '[name].js',
	//		chunkFilename: '[id].bundle.js'
		},
		devtool: 'source-map'
	};
}