import Vue from "vue";
import VueI18n from "vue-i18n";
import en_US from "./i18n/en-US/main";
import DatePicker from "element-ui/lib/date-picker"
import ElementLocale from "element-ui/lib/locale"
import find from "lodash/find";
import includes from "lodash/includes";

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

export const languagesCode = [
    "en-US",
    "zh-CN"
];

const defaultLanguage = "en-US";
const loadedLanguages = [defaultLanguage];
// 获取用户语言
let lang = localStorage.getItem("vvv-lang");
if (!includes(languagesCode, lang)) {
    const browserLanguages = navigator.languages || [navigator.browserLanguage, navigator.systemLanguage, navigator.userLanguage];
    lang = find(browserLanguages, value => includes(languagesCode, value)) || defaultLanguage;
}

export const i18n = new VueI18n({
    locale: lang,
    fallbackLocale: defaultLanguage,
    silentFallbackWarn: true,
    messages,
});

// if (lang !== default_lang) {
//     loadLanguageAsync(lang);
// }

ElementLocale.i18n((key, value) => i18n.t(key, value));

export function loadLanguageAsync (lang, init) {
    if ((init || i18n.locale !== lang) && includes(languagesCode, lang)) {
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
