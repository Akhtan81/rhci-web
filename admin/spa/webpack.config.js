require('webpack');

const PROD = 'production'
const DEV = 'development'

const ENV = process.env.NODE_ENV

if (!ENV)
    throw new Error("NODE_ENV variable is undefined. Should be " + [PROD, DEV].join('|'));

console.log("Building " + ENV + " bundle");

module.exports = ENV === PROD
    ? require('./webpack.prod-config.js')
    : require('./webpack.dev-config.js');