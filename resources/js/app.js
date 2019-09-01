import Vue from "vue";
import router from "./router";
import {i18n, loadLanguageAsync} from "./i18n";
import App from "./components/App";

import {
    Aside,
    Button,
    Checkbox,
    CheckboxGroup,
    Col,
    Container,
    Dialog,
    Dropdown,
    DropdownItem,
    DropdownMenu,
    Form,
    FormItem,
    Header,
    Image,
    Input,
    Link,
    Main,
    Menu,
    MenuItem,
    MenuItemGroup,
    Option,
    PageHeader,
    Pagination,
    Popover,
    Progress,
    Radio,
    RadioGroup,
    Row,
    Select,
    Submenu,
    Switch,
    Table,
    TableColumn,
    Tag,
    Tooltip
} from "element-ui";

import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {library as FortAwesome} from "@fortawesome/fontawesome-svg-core";
import {
    faCog,
    faInfoCircle,
    faList,
    faNetworkWired,
    faPlus,
    faQuestion,
    faRoute,
    faServer,
} from "@fortawesome/free-solid-svg-icons";

FortAwesome.add(faCog, faInfoCircle, faList, faNetworkWired, faPlus, faQuestion, faRoute, faServer);

Vue.component("font-awesome-icon", FontAwesomeIcon);

Vue.use(Aside);
Vue.use(Button);
Vue.use(Checkbox);
Vue.use(CheckboxGroup);
Vue.use(Col);
Vue.use(Container);
Vue.use(Dialog);
Vue.use(Dropdown);
Vue.use(DropdownItem);
Vue.use(DropdownMenu);
Vue.use(Form);
Vue.use(FormItem);
Vue.use(Header);
Vue.use(Image);
Vue.use(Input);
Vue.use(Link);
Vue.use(Main);
Vue.use(Menu);
Vue.use(MenuItem);
Vue.use(MenuItemGroup);
Vue.use(Option);
Vue.use(PageHeader);
Vue.use(Pagination);
Vue.use(Popover);
Vue.use(Progress);
Vue.use(Radio);
Vue.use(RadioGroup);
Vue.use(Row);
Vue.use(Select);
Vue.use(Submenu);
Vue.use(Switch);
Vue.use(Table);
Vue.use(TableColumn);
Vue.use(Tag);
Vue.use(Tooltip);

window.EMPTY_FUNC = () => undefined;

// new Vue({
//     i18n,
//     router,
//     render: h => h(App),
// }).$mount("#app");

// 让语言文件先加载完成，避免页面加载时语言会刷新。
loadLanguageAsync(i18n.locale, true).then(() => {
    new Vue({
        i18n,
        router,
        render: h => h(App),
    }).$mount("#app");
});
