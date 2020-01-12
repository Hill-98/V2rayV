<template>
    <div>
        <el-page-header @back="toBack" :content="action_text">
            <template slot="title">
                <span v-t="'el.pageHeader.title'"></span>
            </template>
        </el-page-header>
        <el-form :disabled="form_disabled" :model="form_data" :rules="form_rules" ref="form" class="vvv-form"
                 label-width="200px">
            <el-form-item :label="$t('common.name')">
                <el-col :span="6">
                    <el-input v-model="form_data.name"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item prop="address" :label="$t('server.edit.address')">
                <el-col :span="6">
                    <el-input v-model="form_data.address" placeholder="e.g: 127.0.0.1"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item prop="port" :label="$t('server.edit.port')">
                <el-col :span="2">
                    <el-input type="number" :min="1" :max="65535" v-model="form_data.port" placeholder="e.g: 12345">
                    </el-input>
                </el-col>
            </el-form-item>
            <el-form-item prop="protocol" :label="$t('common.protocol')">
                <el-select v-model="form_data.protocol" :placeholder="$t('common.please_select')"
                           @change="protocolSelectChange">
                    <el-option v-for="item in v2ray.protocol" :key="item" :label="item" :value="item"></el-option>
                </el-select>
            </el-form-item>
            <protocol-vmess v-if="form_data.protocol === 'vmess'" :setting="form_data.protocol_setting">
            </protocol-vmess>
            <protocol-shadowsocks v-if="form_data.protocol === 'shadowsocks'" :setting="form_data.protocol_setting">
            </protocol-shadowsocks>
            <protocol-socks v-if="form_data.protocol === 'socks'" :setting="form_data.protocol_setting">
            </protocol-socks>
            <el-form-item prop="network" :label="$t('common.network_type')">
                <el-select v-model="form_data.network" :placeholder="$t('common.please_select')"
                           @change="networkSelectChange">
                    <el-option v-for="item in v2ray.network" :key="item.value" :label="item.label" :value="item.value">
                    </el-option>
                </el-select>
            </el-form-item>
            <network-tcp v-if="form_data.network === 'tcp'" :setting="form_data.network_setting"></network-tcp>
            <network-kcp v-if="form_data.network === 'kcp'" :setting="form_data.network_setting"></network-kcp>
            <network-ws v-if="form_data.network === 'ws'" :setting="form_data.network_setting"></network-ws>
            <network-http v-if="form_data.network === 'http'" :setting="form_data.network_setting"></network-http>
            <network-quic v-if="form_data.network === 'quic'" :setting="form_data.network_setting"></network-quic>
            <el-form-item prop="security" :label="$t('server.edit.transfer_encrypt')">
                <el-select v-model="form_data.security" :placeholder="$t('common.please_select')">
                    <el-option label="NONE" value="none"></el-option>
                    <el-option label="TLS" value="tls"></el-option>
                </el-select>
            </el-form-item>
            <template v-if="form_data.security === 'tls'">
                <el-form-item :label="$t('server.edit.cert_domain')">
                    <el-col :span="6">
                        <el-input v-model="form_data.security_setting.serverName"></el-input>
                    </el-col>
                </el-form-item>
                <el-form-item prop="security_setting.alpn" label="ALPN">
                    <el-checkbox-group v-model="form_data.security_setting.alpn">
                        <el-checkbox label="http/1.1">HTTP 1.1</el-checkbox>
                        <el-checkbox label="h2">HTTP 2</el-checkbox>
                    </el-checkbox-group>
                </el-form-item>
                <el-form-item :label="$t('server.edit.allow_insecure')">
                    <el-switch v-model="form_data.security_setting.allowInsecure"></el-switch>
                </el-form-item>
            </template>
            <mux :setting="form_data.mux"></mux>
            <el-form-item :label="$t('server.local_port')">
                <el-col :span="2">
                    <el-input type="number" :min="0" :max="65535" v-model="form_data.local_port" placeholder="e.g: 666">
                    </el-input>
                </el-col>
                <el-tooltip effect="dark" :content="$t('tips.server.local_port')" placement="top">
                    <el-button class="vvv-question-btn" size="mini" circle>
                        <font-awesome-icon class="vvv-menu-icon" icon="question"></font-awesome-icon>
                    </el-button>
                </el-tooltip>
            </el-form-item>
            <el-form-item :label="$t('common.enable')">
                <el-switch v-model="form_data.enable"></el-switch>
            </el-form-item>
            <el-form-item>
                <el-button :disabled="submit_ing" type="primary" v-t="'common.save'" @click="submitForm"></el-button>
            </el-form-item>
        </el-form>
    </div>
</template>

<script>
    import {every, includes, map} from "lodash/collection";
    import {isEmpty, isObject, toInteger} from "lodash/lang";
    import {endsWith ,snakeCase, startsWith, split, trim} from "lodash/string";
    import {MessageBox} from "element-ui";
    /* --------------------------- */
    import mux from "./mux";
    import protocolVmess from "./Protocol/vmess";
    import protocolShadowsocks from "./Protocol/shadowsocks";
    import protocolSocks from "./Protocol/socks";
    import networkTcp from "./Network/tcp";
    import networkKcp from "./Network/kcp";
    import networkWs from "./Network/ws";
    import networkHttp from "./Network/http";
    import networkQuic from "./Network/quic";
    /* --------------------------- */
    import Api from "../../API/Server";
    import format from "../../data/format/server";
    import {editInit, submitForm} from "../../common/index";

    export default {
        name: "ServerEdit",
        data: () => ({
            is_change: false,
            submit_ing: false,
            form_disabled: false,
            form_data: {
                name: "",
                address: "",
                port: "",
                protocol: "",
                protocol_setting: {},
                network: "",
                network_setting: {},
                security: "none",
                security_setting: {
                    alpn: [
                        "http/1.1"
                    ],
                },
                mux: {
                    enabled: false,
                    concurrency: 8
                },
                local_port: "",
                enable: false
            },
            v2ray: {
                protocol: [
                    "vmess",
                    "shadowsocks",
                    "socks"
                ],
                network: [
                    {
                        value: "tcp",
                        label: "TCP"
                    },
                    {
                        value: "kcp",
                        label: "KCP"
                    },
                    {
                        value: "ws",
                        label: "WebSocket"
                    },
                    {
                        value: "http",
                        label: "HTTP"
                    },
                    {
                        value: "quic",
                        label: "QUIC"
                    },
                ]
            },
        }),
        computed: {
            action_text() {
                return this.$t(`title.${snakeCase(this.$route.name)}`);
            },
            form_rules() {
                return {
                    address: [
                        {
                            required: true,
                            message: this.$t("common.can_not_empty", {value: this.$t("server.edit.address")}),
                            trigger: "blur"
                        }
                    ],
                    port: [
                        {
                            validator: (rule, value, callback) => {
                                if (isEmpty(value.toString())) {
                                    callback(new Error(this.$t("common.can_not_empty", {
                                        value: this.$t("server.edit.port")
                                    })));
                                    return;
                                }
                                const port = toInteger(value);
                                if (port > 0 && port <= 65535) {
                                    callback();
                                } else {
                                    callback(new Error(this.$t("common.wrong_input", {
                                        value: this.$t("server.edit.port")
                                    })));
                                }
                            },
                            trigger: "blur",
                        }
                    ],
                    protocol: {
                        required: true,
                        message: this.$t("common.can_not_empty", {value: this.$t("common.protocol")}),
                        trigger: "blur"
                    },
                    protocol_setting: {
                        // vmess
                        id: {
                            required: true,
                            message: this.$t("common.can_not_empty", {value: this.$t("server.edit.user_id")}),
                            trigger: "blur"
                        },
                        security: {
                            required: true,
                            message: this.$t("common.please_select_as", {value: this.$t("server.edit.encrypt_method")}),
                            trigger: "blur"
                        },
                        // vmess end
                        // shadowsocks
                        password: {
                            required: true,
                            message: this.$t("common.can_not_empty", {value: this.$t("common.password")}),
                            trigger: "blur"
                        },
                        method: {
                            required: true,
                            message: this.$t("common.please_select_as", {value: this.$t("server.edit.encrypt_method")}),
                            trigger: "blur"
                        }
                        // shadowsocks end
                    },
                    network: {
                        required: true,
                        message: this.$t("common.can_not_empty", {value: this.$t("common.network_type")}),
                        trigger: "blur"
                    },
                    network_setting: {
                        header: {
                            // tcp kcp
                            type: {
                                required: true,
                                message: this.$t("common.please_select_as", {value: this.$t("server.edit.header_type")}),
                                trigger: "blur"
                            },
                            // tcp kcp end
                            // tcp
                            request: {
                                version: [
                                    {
                                        validator: (rule, value, callback) => {
                                            if (isEmpty(value)) {
                                                callback(new Error(this.$t("common.can_not_empty", {
                                                    value: this.$t("server.edit.http_version")
                                                })));
                                                return;
                                            }
                                            const http_version = [
                                                "1.0",
                                                "1.1",
                                                "2"
                                            ];
                                            if (includes(http_version, value)) {
                                                callback();
                                            } else {
                                                callback(new Error(this.$t("common.wrong_input", {
                                                    value: this.$t("server.edit.http_version")
                                                })));
                                            }
                                        },
                                        trigger: "blur",
                                    }
                                ],
                                method: [
                                    {
                                        validator: (rule, value, callback) => {
                                            if (isEmpty(value)) {
                                                callback(new Error(this.$t("common.can_not_empty", {
                                                    value: this.$t("server.edit.http_method")
                                                })));
                                                return;
                                            }
                                            const http_method = [
                                                "GET",
                                                "POST",
                                                "HEAD",
                                                "PUT",
                                                "DELETE",
                                                "CONNECT",
                                                "OPTIONS",
                                                "TRACE",
                                                "PATCH"
                                            ];
                                            if (includes(http_method, value)) {
                                                callback();
                                            } else {
                                                callback(new Error(this.$t("common.wrong_input", {
                                                    value: this.$t("server.edit.http_method")
                                                })));
                                            }
                                        },
                                        trigger: "blur",
                                    }
                                ],
                                path: [
                                    {
                                        validator: (rule, value, callback) => {
                                            if (isEmpty(value)) {
                                                callback(new Error(this.$t("common.can_not_empty", {
                                                    value: this.$t("common.path")
                                                })));
                                                return;
                                            }
                                            const ok = every(map(split(value, ","), trim), value => {
                                                return startsWith(value, "/")
                                            });
                                            if (ok) {
                                                callback();
                                            } else {
                                                callback(new Error(this.$t("common.wrong_input", {
                                                    value: this.$t("common.path")
                                                })));
                                            }
                                        },
                                        trigger: "blur",
                                    }
                                ],
                                headers: {
                                    validator: (rule, value, callback) => {
                                        if (isEmpty(value)) {
                                            callback();
                                            return;
                                        }
                                        const error = new Error(this.$t("common.wrong_input", {
                                            value: this.$t("server.edit.http_header")
                                        }));
                                        try {
                                            if (isObject(JSON.parse(value))) {
                                                callback();
                                            } else {
                                                callback(error);
                                            }
                                        } catch (e) {
                                            callback(error);
                                        }
                                    },
                                    trigger: "blur",
                                }
                            }
                            // tcp end
                        },
                        // kcp
                        mtu: [
                            {
                                validator: (rule, value, callback) => {
                                    if (isEmpty(value.toString())) {
                                        callback(new Error(this.$t("common.can_not_empty", {value: "MTU"})));
                                        return;
                                    }
                                    value = toInteger(value);
                                    if (value >= 576 && value <= 1460) {
                                        callback();
                                    } else {
                                        callback(new Error(this.$t("common.wrong_input", {value: "MTU"})));
                                    }
                                },
                                trigger: "blur",
                            }
                        ],
                        tti: [
                            {
                                validator: (rule, value, callback) => {
                                    if (isEmpty(value.toString())) {
                                        callback(new Error(this.$t("common.can_not_empty", {value: "TTI"})));
                                        return;
                                    }
                                    value = toInteger(value);
                                    if (value >= 10 && value <= 100) {
                                        callback();
                                    } else {
                                        callback(new Error(this.$t("common.wrong_input", {value: "TTI"})));
                                    }
                                },
                                trigger: "blur",
                            }
                        ],
                        // kcp end
                        // ws http
                        path: [
                            {
                                validator: (rule, value, callback) => {
                                    if (isEmpty(value)) {
                                        callback();
                                        return;
                                    }
                                    const ok = every(map(split(value, ","), trim), value => {
                                        return startsWith(value, "/")
                                    });
                                    if (ok) {
                                        callback();
                                    } else {
                                        callback(new Error(this.$t("common.wrong_input", {
                                            value: this.$t("common.path")
                                        })));
                                    }
                                },
                                trigger: "blur",
                            }
                        ],
                        // ws http end
                        headers: {
                            validator: (rule, value, callback) => {
                                if (isEmpty(value)) {
                                    callback();
                                    return;
                                }
                                const error = this.$t("common.wrong_input", {
                                    value: this.$t("server.edit.http_header")
                                });
                                try {
                                    if (isObject(JSON.parse(value))) {
                                        callback();
                                    } else {
                                        callback(error);
                                    }
                                } catch (e) {
                                    callback(error);
                                }
                            },
                            trigger: "blur",
                        },
                        // ws end
                        // http
                        host: {
                            required: true,
                            message: this.$t("common.can_not_empty", {value: this.$t("common.domain_name")}),
                            trigger: "blur"
                        },
                        // http end
                        // quic
                        security: {
                            required: true,
                            message: this.$t("common.please_select_as", {value: this.$t("server.edit.encrypt_method")}),
                            trigger: "blur"
                        },
                        key: {
                            required: true,
                            message: this.$t("common.can_not_empty", {value: this.$t("server.edit.key")}),
                            trigger: "blur"
                        }
                    },
                    // 传输层加密
                    security: {
                        required: true,
                        message: this.$t("common.can_not_empty", {value: this.$t("server.edit.transfer_encrypt")}),
                        trigger: "blur"
                    },
                    security_setting: {
                        alpn: {
                            required: true,
                            message: this.$t("common.select_least_one", {value: "ALPN"}),
                            trigger: "blur"
                        }
                    },
                    // 传输层加密 end
                };
            }
        },
        components: {
            mux,
            protocolVmess,
            protocolShadowsocks,
            protocolSocks,
            networkTcp,
            networkKcp,
            networkWs,
            networkHttp,
            networkQuic
        },
        props: [
            "id"
        ],
        watch: {
            form_data: {
                handler() {
                    if (!this.is_change) {
                        this.is_change = true
                    }
                },
                deep: true
            }
        },
        created() {
            window.addEventListener("beforeunload", this.beforeunload);
            const edit = endsWith(this.$route.name, "-edit");
            if (edit) {
                editInit(Api, this, format);
            }
        },
        destroyed() {
            window.removeEventListener("beforeunload", this.beforeunload);
        },
        beforeRouteLeave(to, from, next) {
            if (this.is_change) {
                MessageBox.confirm(this.$t("common.ask_to_quit"), this.$t("common.prompt"))
                    .then(() => next())
                    .catch(window.EMPTY_FUNC);
            } else {
                next();
            }
        },
        methods: {
            beforeunload(e) {
                if (this.is_change) e.returnValue = this.$t("common.ask_to_quit");
            },
            toBack() {
                this.$router.push(`/${this.$i18n.locale}/${this.$route.meta.back}`);
            },
            submitForm() {
                submitForm(Api, this, format);
            },
            // 协议选择变更
            protocolSelectChange(value) {
                let o = {};
                switch (value) {
                    case "vmess":
                        o = {security: "auto"};
                        break;
                    case "shadowsocks":
                        o = {ota: false};
                }
                this.form_data.protocol_setting = o;
            },
            // 网络选择变更
            networkSelectChange(value) {
                let o = {};
                switch (value) {
                    case "tcp":
                        o = {
                            header: {
                                type: "none"
                            }
                        };
                        break;
                    case "kcp":
                        o = {
                            mtu: 1460,
                            tti: 50,
                            uplinkCapacity: 5,
                            downlinkCapacity: 5,
                            congestion: false,
                            readBufferSize: 2,
                            writeBufferSize: 2,
                            header: {
                                type: "none",
                            }
                        };
                        break;
                    case "quic":
                        o = {
                            security: "none",
                            header: {
                                type: "none",
                            }
                        };
                }
                this.form_data.network_setting = o;
            },
        }
    }
</script>
