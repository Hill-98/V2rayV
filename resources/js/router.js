import Vue from "vue";
import Router from "vue-router";
import Dns from "./components/Dns";
import Server from "./components/Server/index";
import ServerEdit from "./components/Server/edit";
import RoutingCustom from "./components/Routing/custom"
import RoutingEdit from "./components/Routing/edit";
import Subscribe from "./components/Subscribe/index";
import SubscribeEdit from "./components/Subscribe/edit";
import {i18n, Languages, loadLanguageAsync} from "./i18n";
import {forEach, map} from "lodash/collection";
import concat from "lodash/concat";
import {snakeCase, startsWith, trimStart} from "lodash/string";

Vue.use(Router);

let routes = [
    {
        path: "/servers",
        name: "servers",
        component: Server,
        meta: {
            menuIndex: "servers"
        }
    },
    {
        path: "/servers/add",
        name: "servers-add",
        component: ServerEdit,
        meta: {
            menuIndex: "servers",
            back: "servers"
        }
    },
    {
        path: "/servers/edit/:id",
        name: "servers-edit",
        component: ServerEdit,
        props: true,
        meta: {
            menuIndex: "servers",
            back: "servers"
        }
    },
    {
        path: "/routing/default",
        name: "routing-default",
        component: RoutingEdit,
        props: {
            id: "default"
        },
        meta: {
            menuIndex: "routing/default"
        }
    },
    {
        path: "/routing/custom",
        name: "routing-custom",
        component: RoutingCustom,
        meta: {
            menuIndex: "routing/custom"
        }
    },
    {
        path: "/routing/custom/add",
        name: "routing-add",
        component: RoutingEdit,
        meta: {
            menuIndex: "routing/custom",
            back: "routing/custom"
        }
    },
    {
        path: "/routing/custom/edit/:id",
        name: "routing-edit",
        component: RoutingEdit,
        props: true,
        meta: {
            menuIndex: "routing/custom",
            back: "routing/custom"
        }
    },
    {
        path: "/dns",
        name: "dns",
        component: Dns,
        meta: {
            menuIndex: "dns"
        }
    },
    {
        path: "/subscribe",
        name: "subscribe",
        component: Subscribe,
        meta: {
            menuIndex: "subscribe"
        }
    },
    {
        path: "/subscribe/add",
        name: "subscribe-add",
        component: SubscribeEdit,
        meta: {
            menuIndex: "subscribe",
            back: "subscribe"
        }
    },
    {
        path: "/subscribe/edit/:id",
        name: "subscribe-edit",
        component: SubscribeEdit,
        props: true,
        meta: {
            menuIndex: "subscribe",
            back: "subscribe"
        }
    }
];

// 添加语言参数到路由规则
routes = map(routes, value => {
    if (value !== "/") {
        value.path = `/:lang${value.path}`;
    }
    return value;
});

const redirect = [];

// 为所有页面生成默认语言重定向
forEach(routes, value => {
    if (startsWith(value.path, "/:lang")) {
        const path = trimStart(value.path, "/:lang");
        redirect.push({
            path: `/${path}`,
            redirect: `/${i18n.locale}/${path}`
        })
    }
});
// 为所有语言首页生成重定向
forEach(Languages, value => {
    redirect.unshift(    {
        path: `/${value}`,
        redirect: `/${value}/servers`
    })
});
// 添加主页到默认语言的重定向
redirect.unshift({
    path: "/",
    redirect: `/${i18n.locale}`
});

const router = new Router({
    routes: concat(redirect, routes)
});

// 路由后置钩子：设置语言变更和更改标题
router.afterEach(to => {
    if (to.params.lang) {
        loadLanguageAsync(to.params.lang)
            .finally(() => document.title = `V2rayV - ${i18n.t(`title.${snakeCase(to.name)}`)}`);
    }
});

export default router;
