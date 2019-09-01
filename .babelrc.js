const babel_config = {
    presets: [],
    plugins: [
        [
            "@babel/plugin-syntax-dynamic-import"
        ],
        [
            "@babel/plugin-proposal-class-properties"
        ],
        [
            "component",
            {
                "libraryName": "element-ui",
                "styleLibraryName": "theme-chalk"
            }
        ]
    ]
};
if (process.env.NODE_ENV === "production") {
    babel_config.presets.push([
        "@babel/preset-env",
        {
            useBuiltIns: "usage",
            modules: false,
            corejs: 2,
        }
    ]);
}

module.exports = babel_config;
