module.exports = {
    root: true,
    "env": {
        "browser": true,
        "es6": true
    },
    "extends": [
        "eslint:recommended",
        "plugin:vue/essential",
        "plugin:lodash/recommended",
    ],
    "globals": {
        "Atomics": "readonly",
        "SharedArrayBuffer": "readonly",
    },
    "parserOptions": {
        "parser": "babel-eslint",
    },
    "plugins": [
        "vue",
        "lodash",
        "babel"
    ],
    "rules": {
        "babel/no-invalid-this": 1,
    }
};
