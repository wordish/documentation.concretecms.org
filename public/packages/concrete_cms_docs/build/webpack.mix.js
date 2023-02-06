/**
 * @project:   ConcreteCMS Docs
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

let mix = require('laravel-mix');

mix.webpackConfig({
    resolve: {
        symlinks: false
    },
    externals: {
        jquery: 'jQuery',
        bootstrap: true,
        vue: 'Vue',
        moment: 'moment'
    },
    module: {
        rules: [
            { test: /\.html$/, loader: "underscore-template-loader" },
            {
                test: /\.jsx?$/,
                exclude: /(bower_components|node_modules\/v-calendar)/,
                use: [
                    {
                        loader: 'babel-loader',
                        options: Config.babel()
                    }
                ]
            },
        ]
    }
});

mix.setResourceRoot('../');
mix.setPublicPath('../');

mix
    .sass('assets/docs/scss/main.scss', '../css/concrete-cms-docs.css')
    .js('assets/docs/js/main.js', '../js/concrete-cms-docs.js').vue()
