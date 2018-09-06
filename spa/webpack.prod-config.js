const WebpackStripLoader = require('strip-loader');
const config = require('./webpack.dev-config.js');

config.module.loaders.push({
    test: /\.js$/,
    exclude: /node_modules/,
    loader: WebpackStripLoader.loader('console.log')
});

config.plugins.push(new webpack.optimize.UglifyJsPlugin());

delete config.devtool

module.exports = config;
