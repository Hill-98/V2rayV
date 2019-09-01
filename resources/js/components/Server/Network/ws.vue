<template>
    <div>
        <el-form-item prop="network_setting.path" :label="$t('common.path')">
            <el-col :span="6">
                <el-input v-model="setting.path" placeholder="e.g: /example"></el-input>
            </el-col>
        </el-form-item>
        <el-form-item prop="network_setting.headers" :label="$t('server.edit.http_header')">
            <el-col :span="6">
                <el-input type="textarea" :autosize="true" v-model="setting.headers"
                          :placeholder="$t('server.edit.json_object_format')"></el-input>
            </el-col>
            <el-tooltip effect="dark" :content="$t('common.click_me')" placement="top">
                <el-button class="vvv-question-btn" size="mini" circle @click="tip_box">
                    <font-awesome-icon class="vvv-menu-icon" icon="question"></font-awesome-icon>
                </el-button>
            </el-tooltip>
        </el-form-item>
    </div>
</template>

<script>
    import replace from "lodash/replace"
    import {MessageBox} from "element-ui";

    export default {
        name: "NetworkWS",
        data: () => ({
            headers_tip: "{\n" +
                "    \"Host\": \"v2ray.com\"\n" +
                "  }"
        }),
        props: [
            "setting"
        ],
        methods: {
            tip_box() {
                MessageBox({
                    title: this.$t("server.edit.http_header_example_config"),
                    message: replace(replace(this.headers_tip, /\n/g, "<br>"), /\s/g, "&nbsp;"),
                    confirmButtonText: this.$t("common.i_know"),
                    dangerouslyUseHTMLString: true
                });
            }
        }
    }
</script>
