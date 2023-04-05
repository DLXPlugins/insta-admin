const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );
const path = require( 'path' );
module.exports = ( env ) => {
	return [
		{
			...defaultConfig,
			module: {
				...defaultConfig.module,
				rules: [ ...defaultConfig.module.rules ],
			},
			mode: env.mode,
			devtool: 'source-map',
		},
		{
			entry: {
				'insta-admin-landing-page': [ './src/scss/admin-landing.scss' ],
				'insta-admin-block-editor': [ './src/scss/admin-block-editor.scss' ],
				'insta-gfont-ubuntu': { import: './src/scss/fonts/ubuntu.scss' },
				'insta-gfont-lato': { import: './src/scss/fonts/lato.scss' },
			},
			mode: env.mode,
			devtool: 'production' === env.mode ? false : 'source-map',
			output: {
				filename: '[name].js',
				sourceMapFilename: '[file].map[query]',
				assetModuleFilename: 'fonts/[name][ext]',
				clean: true,
			},
			resolve: {
				alias: {
					react: path.resolve( 'node_modules/react' ),
					'react-dom': path.resolve( 'node_modules/react-dom' ),
					'@wordpress/i18n': path.resolve( 'node_modules/@wordpress/i18n' ),
					'@wordpress/element': path.resolve( 'node_modules/@wordpress/element' ),
					'@wordpress/components': path.resolve( 'node_modules/@wordpress/components' ),
					'@wordpress/block-editor': path.resolve( 'node_modules/@wordpress/block-editor' ),
					'@wordpress/hooks': path.resolve( 'node_modules/@wordpress/hooks' ),

				},
			},
			module: {
				rules: [
					{
						test: /\.(js|jsx)$/,
						exclude: /(node_modules|bower_components)/,
						loader: 'babel-loader',
						options: {
							presets: [ '@babel/preset-env', '@babel/preset-react' ],
							plugins: [
								'@babel/plugin-proposal-class-properties',
								'@babel/plugin-transform-arrow-functions',
								'lodash',
							],
						},
					},
					{
						test: /\.scss$/,
						exclude: /(node_modules|bower_components)/,
						use: [
							{
								loader: MiniCssExtractPlugin.loader,
							},
							{
								loader: 'css-loader',
								options: {
									sourceMap: true,
								},
							},
							{
								loader: 'resolve-url-loader',
							},
							{
								loader: 'sass-loader',
								options: {
									sourceMap: true,
								},
							},
						],
					},
					{
						test: /\.css$/,
						include: [
							path.resolve(
								__dirname,
								'node_modules/@wordpress/components/build-style/style.css'
							),
						],
						use: [
							{
								loader: MiniCssExtractPlugin.loader,
							},
							{
								loader: 'css-loader',
								options: {
									sourceMap: true,
								},
							},
							'sass-loader',
						],
					},
					{
						test: /\.(woff2?|ttf|otf|eot|svg)$/,
						include: [ path.resolve( __dirname, 'fonts' ) ],
						exclude: /(node_modules|bower_components)/,
						type: 'asset/resource',
					},
				],
			},
			plugins: [ new RemoveEmptyScriptsPlugin(), new MiniCssExtractPlugin() ],
		},
	];
};

