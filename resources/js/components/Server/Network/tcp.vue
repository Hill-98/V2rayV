<template>
    <div>
        <el-form-item prop="network_setting.header.type" :label="$t('server.edit.header_type')">
            <el-select v-model="setting.header.type" :placeholder="$t('common.please_select')"
                       @change="headerSelectChange">
                <el-option label="NONE" value="none"></el-option>
                <el-option label="HTTP" value="http"></el-option>
            </el-select>
        </el-form-item>
        <template v-if="setting.header.type === 'http'">
            <el-form-item prop="network_setting.header.request.version" :label="$t('server.edit.http_version')">
                <el-col :span="2">
                    <el-input v-model="setting.header.request.version" placeholder="e.g: 1.1"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item prop="network_setting.header.request.method" :label="$t('server.edit.http_method')">
                <el-col :span="2">
                    <el-input v-model="setting.header.request.method" placeholder="e.g: GET"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item prop="network_setting.header.request.path" :label="$t('common.path')">
                <el-col :span="6">
                    <el-input type="textarea" :autosize="true" v-model="setting.header.request.path"
                              :placeholder="`e.g: /example ${$t('common.value_split')}`">
                    </el-input>
                </el-col>
            </el-form-item>
            <el-form-item prop="network_setting.header.request.headers" :label="$t('server.edit.http_header')">
                <el-col :span="12">
                    <el-input type="textarea" :autosize="true" v-model="setting.header.request.headers"
                              :placeholder="$t('server.edit.json_object_format')">
                    </el-input>
                </el-col>
                <el-tooltip effect="dark" :content="$t('common.click_me')" placement="top">
                    <el-button class="vvv-question-btn" size="mini" circle @click="tip_box">
                        <font-awesome-icon class="vvv-menu-icon" icon="question"></font-awesome-icon>
                    </el-button>
                </el-tooltip>
            </el-form-item>
        </template>
    </div>
</template>

<script>
    import replace from "lodash/replace"
    import {MessageBox} from 'element-ui';

    export default {
        name: "NetworkTcp",
        data: () => ({
            headers_tip: "{\n" +
                "    \"Host\": [\"www.baidu.com\", \"www.bing.com\"],\n" +
                "    \"User-Agent\": [\n" +
                "      \"Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36\",\n" +
                "      \"Mozilla/5.0 (iPhone; CPU iPhone OS 10_0_2 like Mac OS X) AppleWebKit/601.1 (KHTML, like Gecko) CriOS/53.0.2785.109 Mobile/14A456 Safari/601.1.46\"\n" +
                "    ],\n" +
                "    \"Accept-Encoding\": [\"gzip, deflate\"],\n" +
                "    \"Connection\": [\"keep-alive\"],\n" +
                "    \"Pragma\": \"no-cache\"\n" +
                "  }"
        }),
        props: [
            "setting"
        ],
        methods: {
            tip_box() {
                const style = document.createElement('style');
                style.type = 'text/css';
                style.innerHTML = ".vvv-message-box {width: 1100px;}";
                document.head.appendChild(style);
                MessageBox({
                    title: this.$t("server.edit.http_header_example_config"),
                    message: replace(replace(this.headers_tip, /\n/g, "<br>"), /\s/g, "&nbsp;"),
                    confirmButtonText: this.$t("common.i_know"),
                    dangerouslyUseHTMLString: true,
                    customClass: "vvv-message-box"
                }).finally(() => setTimeout(style.remove, 2000));
            },
            headerSelectChange(value) {
                if (value === "http") {
                    this.$set(this.setting.header, "request", {})
                } else {
                    this.$delete(this.setting.header, "request")
                }
            }
        }
    }
</script>
