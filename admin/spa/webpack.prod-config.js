//const UglifyJsPlugin = require('uglifyjs-webpack-plugin')
const config = require('./webpack.dev-config.js');

config.mode = 'production'
config.optimization = {
    /*minimizer: [
        new UglifyJsPlugin({
            parallel: 2,
            uglifyOptions: {
                compress: {
                    inline: false
                }
            }
        })
    ],*/
    runtimeChunk: false,
    splitChunks: {
        cacheGroups: {
            default: false,
            commons: {
                test: /[\\/]node_modules[\\/]/,
                name: 'vendor_app',
                chunks: 'all',
                minChunks: 2
            }
        }
    }
}

module.exports = config;
