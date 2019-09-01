module.exports = {
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
        "ecmaVersion": 2018,
        "sourceType": "module"
    },
    "plugins": [
        "vue",
        "lodash",
        "babel"
    ],
    "rules": {
        "no-empty": [
            "error",
            {
                "allowEmptyCatch": true
            }
        ],
        "babel/no-invalid-this": 1,
    }
};
