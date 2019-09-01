<template>
    <el-container>
        <el-header class="vvv-header">
            <vvv-header></vvv-header>
        </el-header>
        <el-container>
            <el-aside width="200px">
                <vvv-menu></vvv-menu>
            </el-aside>
            <el-main>
                <router-view :key="router_key"></router-view>
            </el-main>
        </el-container>
    </el-container>
</template>

<script>
    import toInteger from "lodash/toInteger";
    import trimStart from "lodash/trimStart";
    import vvvHeader from "./Header";
    import vvvMenu from "./menu";
    import VersionApi from "../API/Version";

    export default {
        name: "App",
        components: {
            vvvHeader,
            vvvMenu
        },
        computed: {
            router_key() {
                let path = this.$route.fullPath;
                const lang = this.$route.params.lang;
                if (lang) {
                    path = trimStart(path, `/${lang}`)
                }
                return path;
            }
        },
        created() {
            const last_check_time = localStorage.getItem("vvv_check_update_time");
            const curr_time = new Date().getTime() / 1000;
            // 每 3 天检查一次更新
            if (toInteger(last_check_time) < curr_time - 60 * 60 * 24 * 3) {
                VersionApi.check(true)
                    .then(data => {
                        VersionApi.checkVVV(data);
                    })
                    .catch(window.EMPTY_FUNC);
                localStorage.setItem("vvv_check_update_time", curr_time.toString());
            }
        },
        methods: {
            refresh() {
                this.$forceUpdate();
            }
        }
    }
</script>
