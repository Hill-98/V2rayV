<template>
    <el-menu style="height: 93.8vh" :default-active="active" router>
        <template v-for="item in menuItems">
            <el-submenu v-if="item.sub" :key="item.index" :index="`/${lang}/${item.index}`">
                <template slot="title">
                    <font-awesome-icon class="vvv-menu-icon" :icon="item.icon"></font-awesome-icon>
                    <span v-t="item.title"></span>
                </template>
                <el-menu-item v-for="_item in item.items" :key="_item.index" :index="`/${lang}/${_item.index}`">
                    <template slot="title">
                        <span v-t="_item.title"></span>
                    </template>
                </el-menu-item>
            </el-submenu>
            <el-menu-item v-else :key="item.index" :index="`/${lang}/${item.index}`">
                <template slot="title">
                    <font-awesome-icon class="vvv-menu-icon" :icon="item.icon"></font-awesome-icon>
                    <span v-t="item.title"></span>
                </template>
            </el-menu-item>
        </template>
    </el-menu>
</template>

<script>
    export default {
        name: "Menu",
        data: () => ({
            menuItems: [
                {
                    index: "servers",
                    title: "menu.servers",
                    icon: "server"
                },
                {
                    index: "routing",
                    title: "menu.routing",
                    icon: "route",
                    sub: true,
                    items: [
                        {
                            index: "routing/default",
                            title: "menu.routing_default",
                        },
                        {
                            index: "routing/custom",
                            title: "menu.routing_custom",
                        }
                    ]
                },
                {
                    index: "dns",
                    title: "menu.dns",
                    icon: "network-wired"
                },
                {
                    index: "subscribe",
                    title: "menu.subscribe",
                    icon: "list"
                }
            ]
        }),
        computed: {
            active() {
                return `/${this.lang}/${this.$route.meta.menuIndex}`;
            },
            lang() {
                return this.$i18n.locale
            }
        }
    }
</script>
