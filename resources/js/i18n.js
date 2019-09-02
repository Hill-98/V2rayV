import Vue from "vue";
import VueI18n from "vue-i18n";
import en_US from "./i18n/en-US/main";
import DatePicker from "element-ui/lib/date-picker"
import ElementLocale from "element-ui/lib/locale"
import forEach from "lodash/forEach";
import includes from "lodash/includes";
import isString from "lodash/isString";

Vue.use(VueI18n);
Vue.use(DatePicker);

// 设置语言
function setI18nLanguage (lang) {
    i18n.locale = lang;
    document.documentElement.lang = lang;
    localStorage.setItem("vvv-lang", lang);
    return lang;
}

const messages = {
    "en-US": en_US
};

export const Languages = [
    "en-US",
    "zh-CN"
];

const default_lang = "en-US";
const loadedLanguages = [default_lang];
// 获取用户语言
let lang = localStorage.getItem("vvv-lang");
if (!isString(lang) || !includes(Languages, lang)) {
    // 遍历浏览器语言环境
    forEach(navigator.languages, value => {
        if (includes(Languages, value)) {
            lang = value;
            return false;
        }
    });
}

if (!isString(lang)) {
    lang = default_lang
}

export const i18n = new VueI18n({
    locale: lang,
    fallbackLocale: default_lang,
    silentFallbackWarn: true,
    messages,
});

// if (lang !== default_lang) {
//     loadLanguageAsync(lang);
// }

ElementLocale.i18n((key, value) => i18n.t(key, value));

export function loadLanguageAsync (lang, init) {
    if ((init || i18n.locale !== lang) && includes(Languages, lang)) {
        if (!includes(loadedLanguages, lang)) {
            return import(
                /* webpackInclude: /main\.js$/ */
                /* webpackChunkName: "js/lang/[request]" */
                `./i18n/${lang}/main`).then(module => {
                i18n.setLocaleMessage(lang, module.default);
                loadedLanguages.push(lang);
                return setI18nLanguage(lang);
            })
        }
        return Promise.resolve(setI18nLanguage(lang));
    }
    return Promise.resolve(lang);
}
