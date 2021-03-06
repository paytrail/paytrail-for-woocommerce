const webpack           = require( 'webpack' );
const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );
const path              = require( 'path' ); // This resolves into the absolute path of the theme root.
const env               = process.env.NODE_ENV;

const postCss = {
    loader: 'postcss-loader',
    options: {
        sourceMap: true
    }
};

const cssLoader = {
    loader: 'css-loader',
    options: {
        sourceMap: true,
        minimize: true
    }
};

const sassLoader = {
    loader: 'sass-loader',
    options: {
        sourceMap: true
    }
};

const config = {
    entry: {
        main: './assets/js/main.js'
    },
    output: {
        path: path.resolve( './assets/dist' ),
        filename: '[name].js',
        library: 'paytrail',
        libraryTarget: 'umd',
        umdNamedDefine: true
    },
    externals: {

        // Set jQuery to be an external resource.
        'jquery': 'jQuery'
    },
    plugins: [

        // Extract all css into one file.
        new ExtractTextPlugin( '[name].css', {
            allChunks: true
        }),

        // Provide jQuery instance for all modules.
        new webpack.ProvidePlugin({
            jQuery: 'jquery'
        })
    ],
    module: {
        rules: [
            {
                test: /\.js$/,
                include: [
                    path.resolve( __dirname, 'assets/js' )
                ],
                use: {
                    loader: 'babel-loader',
                    options: {

                        // Removes unneeded whitespace
                        compact: true,

                        // Do not use the .babelrc configuration file.
                        babelrc: false,

                        // The loader will cache the results of the loader in node_modules/.cache/babel-loader.
                        cacheDirectory: true,

                        // List enabled ECMAScript feature sets.
                        presets: [ 'env', 'stage-0' ],

                        // The 'transform-runtime' plugin tells babel to require the runtime instead of inlining it.
                        plugins: [ 'transform-runtime' ]
                    }
                }
            },
            {
                test: /\.css$/,
                use: ExtractTextPlugin.extract({
                    use: [ cssLoader, postCss ]
                })
            },
            {
                test: /\.scss$/,
                include: [
                    path.resolve( __dirname, 'assets/scss' )
                ],
                use: ExtractTextPlugin.extract({
                    use: [ cssLoader, postCss, sassLoader ]
                })
            },
            {
                test: /\.(woff(2)?|eot|ttf|otf)(\?[a-z0-9=\.]+)?$/,
                use: {
                    loader: 'url-loader?name=../fonts/[name].[ext]'
                }
            },
            {
                test: /\.(svg|gif|png|jpeg|jpg)(\?[a-z0-9=\.]+)?$/,
                use: {
                    loader: 'url-loader?name=../images/[name].[ext]'
                }
            }
        ]
    },
    watchOptions: {
        poll: 500
    }
};

if ( env === 'production' ) {
    config.plugins.push(

        // Minify for the production environment.
        new webpack.optimize.UglifyJsPlugin({
            sourceMap: true,
            parallel: true,
            mangle: false,
            compress: {
                unused: false
            }
        })
    );
}

module.exports = config;
