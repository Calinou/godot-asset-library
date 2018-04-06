const path = require('path');
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');

module.exports = {
    entry: './public-src/js/index.js',

    module: {
        rules: [
            {
                test: /\.(scss|css)$/,
                use: ExtractTextPlugin.extract({
                    use: [
                        {
                            loader: 'css-loader',
                        },
                        {
                            loader: 'sass-loader',
                            options: {
                                outputStyle: 'compressed',
                            },
                        },
                    ],
                    fallback: 'style-loader',
                }),
            },
            {
                test: /.(ttf|otf|eot|svg|woff(2)?)(\?[a-z0-9]+)?$/,
                use: [{
                    loader: 'file-loader',
                    options: {
                        name: '[name].[ext]',
                        outputPath: 'fonts/',
                        publicPath: '',
                    },
                }],
            },
            {
                test: /\.(jpg|png|gif|webp)$/,
                loader: 'file-loader?name=img/[name].[ext]',
            },
        ],
    },

    plugins: [
        new UglifyJSPlugin(),
        new ExtractTextPlugin('style.css'),
    ],

    output: {
        filename: 'bundle.js',
        path: path.resolve(__dirname, 'public')
    }
};
