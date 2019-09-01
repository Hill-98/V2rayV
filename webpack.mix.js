const mix = require("laravel-mix");

mix.options({
        extractVueStyles: true,
});

mix.webpackConfig(
    {
        module: {
            rules: [
            {
                enforce: "pre",
                test: /\.(js|vue)$/,
                exclude: /node_modules/,
                loader: "eslint-loader",
            },
            {
                test: /\.js$/,
                exclude: /node_modules/,
                loader: "babel-loader",
            },
            ]
        },
    }
);
mix.disableNotifications();
// mix.disableSuccessNotifications();

mix.js("resources/js/app.js", "public/js")
    .sass("resources/sass/app.sass", "public/css")
    .sourceMaps(false, "source-map");

if (mix.inProduction()) {
    mix.version();
}
