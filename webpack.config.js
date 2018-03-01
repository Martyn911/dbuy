var Encore = require('@symfony/webpack-encore');
var webpack = require('webpack');
var moment = require('moment');
Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .autoProvidejQuery()
    .autoProvideVariables({
        "window.Bloodhound": require.resolve('bloodhound-js'),
        "jQuery.tagsinput": "bootstrap-tagsinput"
    })
    .enableSassLoader()
    .enableVersioning(false)
    .createSharedEntry('js/common', ['jquery'])
    .addEntry('js/app', './assets/js/app.js')
    .addEntry('js/login', './assets/js/login.js')
    .addEntry('js/admin', './assets/js/admin.js')
    .addEntry('js/search', './assets/js/search.js')
    .addEntry('js/datatables', './assets/js/datatables.js')
    .addStyleEntry('css/app', ['./assets/scss/app.scss'])
    .addStyleEntry('css/admin', ['./assets/scss/admin.scss'])
    .addPlugin(new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/))
;

module.exports = Encore.getWebpackConfig();
