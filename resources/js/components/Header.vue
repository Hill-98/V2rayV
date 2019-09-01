<template>
    <div style="height: 100%">
        <el-row type="flex" justify="space-between" align="middle" style="height: 100%">
            <div class="vvv-title">
                <span>V2rayV</span>
            </div>
            <div class="vvv-header-action">
                <div class="vvv-header-icon" style="margin: 0 20px">
                    <span :title="$t('menu.about')" @click="aboutDialogVisible = true"><font-awesome-icon icon="info-circle"></font-awesome-icon></span>
                    <span :title="$t('menu.setting')" @click="settingDialogVisible = true"><font-awesome-icon icon="cog"></font-awesome-icon></span>
                </div>
                <div>
                    <el-dropdown trigger="click" @command="changLanguage">
                <span class="el-dropdown-link vvv-language">
                    {{ $t("common.language") }}
                    <span class="el-icon-arrow-down el-icon--right"></span>
                </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item command="en-US">English</el-dropdown-item>
                            <el-dropdown-item command="zh-CN">简体中文</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </div>
            </div>
        </el-row>
        <el-dialog
            :title="$t('title.about')"
            :visible.sync="aboutDialogVisible"
            destroy-on-close
            width="600px">
            <about></about>
        </el-dialog>
        <el-dialog
            :title="$t('title.setting')"
            :visible.sync="settingDialogVisible"
            destroy-on-close
            width="700px">
            <setting @close="settingDialogVisible = false"></setting>
        </el-dialog>
    </div>
</template>

<script>
    import trimStart from "lodash/trimStart";
    import About from "./About";
    import Setting from "./Setting";

    export default {
        name: "Header",
        data: () => ({
            aboutDialogVisible: false,
            settingDialogVisible: false
        }),
        components: {
            About,
            Setting
        },
        methods: {
            changLanguage(value) {
                if (!this.$route.params.lang) {
                    return;
                }
                const path = `/${value}/${trimStart(this.$route.fullPath, `/${this.$route.params.lang}`)}`;
                // eslint-disable-next-line lodash/prefer-lodash-method
                this.$router.replace(path);
            }
        }
    }
</script>
