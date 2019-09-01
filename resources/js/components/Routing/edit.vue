<template>
    <div>
        <el-page-header v-if="!default_rule" @back="toBack" :content="action_text">
            <template slot="title">
                <span v-t="'el.pageHeader.title'"></span>
            </template>
        </el-page-header>
        <p style="color: #E6A23C" v-if="default_rule"><b v-t="'routing.default.title'"></b></p>
        <el-form :disabled="form_disabled" :model="form_data" :rules="form_rules" ref="form" class="vvv-form"
                 label-width="150px">
            <el-form-item v-if="!default_rule" :label="$t('common.name')">
                <el-col :span="6">
                    <el-input v-model="form_data.name"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item :label="$t('routing.edit.proxy_address')">
                <el-checkbox-group v-model="form_data.proxy.ext">
                    <el-checkbox v-for="item in default_value.ext_rules" :key="item.value" :label="item.value">
                        {{ $t(`routing.edit.ext_rules.${item.text}`) }}
                    </el-checkbox>
                </el-checkbox-group>
                <el-col :span="10">
                    <el-input type="textarea" :autosize="true" v-model="form_data.proxy.custom"
                              :placeholder="$t('routing.edit.custom_placeholder')">
                    </el-input>
                </el-col>
                <el-tooltip effect="dark" :content="$t('common.click_me')" placement="top">
                    <el-button class="vvv-question-btn" size="mini" circle @click="tip_box">
                        <font-awesome-icon class="vvv-menu-icon" icon="question"></font-awesome-icon>
                    </el-button>
                </el-tooltip>
            </el-form-item>
            <el-form-item :label="$t('routing.edit.direct_address')">
                <el-checkbox-group v-model="form_data.direct.ext">
                    <el-checkbox v-for="item in default_value.ext_rules" :key="item.value" :label="item.value">
                        {{ $t(`routing.edit.ext_rules.${item.text}`) }}
                    </el-checkbox>
                </el-checkbox-group>
                <el-col :span="10">
                    <el-input type="textarea" :autosize="true" v-model="form_data.direct.custom"
                              :placeholder="$t('routing.edit.custom_placeholder')">
                    </el-input>
                </el-col>
                <el-tooltip effect="dark" :content="$t('common.click_me')" placement="top">
                    <el-button class="vvv-question-btn" size="mini" circle @click="tip_box">
                        <font-awesome-icon class="vvv-menu-icon" icon="question"></font-awesome-icon>
                    </el-button>
                </el-tooltip>
            </el-form-item>
            <el-form-item :label="$t('routing.edit.block_address')">
                <el-checkbox-group v-model="form_data.block.ext">
                    <el-checkbox v-for="item in default_value.ext_rules" :key="item.value" :label="item.value">
                        {{ $t(`routing.edit.ext_rules.${item.text}`) }}
                    </el-checkbox>
                </el-checkbox-group>
                <el-col :span="10">
                    <el-input type="textarea" :autosize="true" v-model="form_data.block.custom"
                              :placeholder="$t('routing.edit.custom_placeholder')">
                    </el-input>
                </el-col>
                <el-tooltip effect="dark" :content="$t('common.click_me')" placement="top">
                    <el-button class="vvv-question-btn" size="mini" circle @click="tip_box">
                        <font-awesome-icon class="vvv-menu-icon" icon="question"></font-awesome-icon>
                    </el-button>
                </el-tooltip>
            </el-form-item>
            <el-form-item prop="port" :label="$t('routing.target_port')">
                <el-col :span="6">
                    <el-input v-model="form_data.port" :placeholder="`e.g: 80, 21-8080 ${$t('common.value_split')}`">
                    </el-input>
                </el-col>
            </el-form-item>
            <el-form-item prop="network" :label="$t('routing.target_network')">
                <el-radio-group v-model="form_data.network">
                    <el-radio v-for="i in default_value.network" :key="i" :label="i">
                        {{ i.toUpperCase() }}
                    </el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item :label="$t('routing.target_protocol')">
                <el-checkbox-group v-model="form_data.protocol">
                    <el-checkbox v-for="i in default_value.protocol" :key="i" :label="i"></el-checkbox>
                </el-checkbox-group>
            </el-form-item>
            <template v-if="!default_rule">
                <el-form-item prop="servers" :label="$t('routing.edit.target_server')">
                    <el-select v-model="form_data.servers" multiple clearable filterable reserve-keyword
                               :loading="servers_loading" @change="serverSelect">
<!--                        <el-option :label="$t('common.all')" value="all">-->
                        <el-option v-for="item in servers" :key="item.value" :label="item.label" :value="item.value">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item :label="$t('common.enable')">
                    <el-switch v-model="form_data.enable"></el-switch>
                </el-form-item>
            </template>
            <el-form-item>
                <el-button :disabled="submit_ing" type="primary" v-t="'common.save'" @click="submitForm"></el-button>
            </el-form-item>
        </el-form>
    </div>
</template>

<script>
    import {every, includes, map} from "lodash/collection";
    import isEmpty from "lodash/isEmpty";
    import {endsWith, snakeCase, split, trim} from "lodash/string";
    import {Message, MessageBox} from "element-ui";
    import extRules from "../../data/routing/extRules";
    import format from "../../data/format/routing";
    import routingApi from "../../API/Routing";
    import serverApi from "../../API/Server";
    import {editInit, submitForm} from "../../common/index";

    export default {
        name: "RoutingEdit",
        data: () => ({
            concat_servers: false,
            default_rule: false,
            form_disabled: false,
            is_change: false,
            submit_ing: false,
            servers_loading: false,
            server_items: [],
            default_value: {
                ext_rules: extRules,
                network: [
                    "tcp",
                    "udp",
                    "tcp,udp"
                ],
                protocol: [
                    "http",
                    "tls",
                    "bittorrent"
                ]
            },
            form_data: {
                name: "",
                proxy: {
                    custom: "",
                    ext: [],
                },
                direct: {
                    custom: "",
                    ext: [],
                },
                block: {
                    custom: "",
                    ext: [],
                },
                port: "",
                network: "tcp,udp",
                protocol: [],
                servers: []
            },
        }),
        computed: {
            action_text() {
                return this.$t(`title.${snakeCase(this.$route.name)}`);
            },
            default_server() {
                return {
                    value: "all",
                    label: this.$t("common.all")
                }
            },
            servers() {
                const all = {
                    value: "all",
                    label: this.$t("common.all")
                };
                const items = [all];
                if (this.concat_servers) {
                    for (const s of this.server_items) {
                        items.push(s);
                    }
                }
                return items;
            },
            form_rules() {
                return {
                    network: {
                        required: true,
                        message: this.$t("common.please_select_as", {value: this.$t("routing.target_network")}),
                        trigger: "blur"
                    },
                    port: {
                        validator: (rule, value, callback) => {
                            if (isEmpty(value)) {
                                callback();
                                return;
                            }
                            const ok = every(map(split(value, ","), trim), value => {
                                return every(split(value, "-"), port => {
                                    return port > 0 && port <= 65535;
                                })
                            });
                            if (ok) {
                                callback();
                            } else {
                                callback(new Error(this.$t("common.wrong_input",{
                                    value: this.$t("routing.edit.target_port")
                                })));
                            }
                        },
                        trigger: "blur",
                    },
                    servers: {
                        required: true,
                        message: this.$t("common.please_select_as", {value: this.$t("routing.edit.target_server")}),
                        trigger: "blur"
                    },
                };
            }
        },
        props: [
            "id",
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
            this.default_rule = this.id === "default";
            const edit = endsWith(this.$route.name, "-edit");
            if (this.default_rule || edit) {
                editInit(routingApi, this, format, () => {
                    if (!this.default_rule && this.form_data.servers.length !== 0 &&
                        this.form_data.servers[0] !== "all") {
                        this.concat_servers = true;
                    }
                });
            }
            if (!this.default_rule) {
                this.servers_loading = true;
                serverApi.all()
                    .then(data => {
                        this.server_items = map(data.data, value => {
                            return {
                                value: value.id,
                                label: value.name,
                            }
                        });
                        if (!edit) {
                            this.concat_servers = true;
                        }
                    })
                    .catch(window.EMPTY_FUNC)
                    .finally(() => this.servers_loading = false);
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
            toBack() {
                // 如果是默认规则 不要进行操作
                if (!this.default_rule) {
                    this.$router.push(`/${this.$i18n.locale}/${this.$route.meta.back}`);
                }
            },
            beforeunload(e) {
                if (this.is_change) {
                    this.$emit("toggleDefault");
                    e.returnValue = this.$t("common.ask_to_quit");
                }
            },
            serverSelect(value) {
                if (includes(value, "all") && this.servers.length !== 1) {
                    this.form_data.servers = ["all"];
                    this.concat_servers = false;
                } else if (this.servers.length === 1) {
                    this.concat_servers = true;
                }
            },
            tip_box() {
                MessageBox({
                    title: this.$i18n.t("routing.edit.custom_address"),
                    message: this.$i18n.t("tips.routing.custom_address"),
                    confirmButtonText: this.$i18n.t("common.i_know"),
                    dangerouslyUseHTMLString: true,
                })
            },
            submitForm() {
                submitForm(routingApi, this, format, () => {
                    if (this.default_rule) {
                        Message.success(this.$i18n.t("routing.edit.save_success"));
                    }
                    // 如果是默认规则，恢复提交状态
                    this.submit_ing = !this.default_rule;
                    this.is_change = false;
                    this.toBack();
                });
            },
        }
    }
</script>
