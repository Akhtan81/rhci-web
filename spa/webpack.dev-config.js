const path = require('path')

module.exports = {
    mode: 'development',
    entry: {
        app: ['babel-polyfill', './src'],
    },
    output: {
        path: path.resolve(__dirname + '/../public/js/dist'),
        filename: '[name].js'
    },
    module: {
        rules: [
            {
                test: /.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/,
                query: {
                    presets: ['babel-preset-react-app'],
                }
            }
        ]
    },
    devtool: 'source-map'
};
