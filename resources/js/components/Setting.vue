<template>
    <el-form :disabled="form_disabled" :model="form_data" :rules="form_rules" ref="form"
              label-width="220px">
        <el-form-item prop="main_port" :label="$t('setting.main_port')">
            <el-col :span="5">
                <el-input type="number" :min="1" :max="65535" v-model="form_data.main_port"></el-input>
            </el-col>
        </el-form-item>
        <el-form-item prop="main_http_port" :label="$t('setting.main_http_port')">
            <el-col :span="5">
                <el-input type="number" :min="0" :max="65535" v-model="form_data.main_http_port"></el-input>
            </el-col>
            <span style="font-size: 12px; margin-left: 10px" v-t="'setting.close_http_proxy'">
            </span>
        </el-form-item>
        <el-form-item :label="$t('setting.allow_lan')">
            <el-switch v-model="form_data.allow_lan"></el-switch>
        </el-form-item>
            <el-form-item prop="log_level" :label="$t('setting.log_level')">
            <el-select v-model="form_data.log_level" :placeholder="$t('common.please_select')">
                <el-option v-for="item in log_level" :key="item" :label="item" :value="item.value">
                </el-option>
            </el-select>
        </el-form-item>
        <el-form-item :label="$t('setting.auto_update_v2ray')">
            <el-switch v-model="form_data.auto_update_v2ray"></el-switch>
        </el-form-item>
        <el-form-item :label="$t('setting.update_v2ray_proxy')">
            <el-switch v-model="form_data.update_v2ray_proxy"></el-switch>
        </el-form-item>
        <el-form-item :label="$t('setting.auto_start')">
            <el-switch v-model="form_data.auto_start"></el-switch>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" v-t="'common.save'" @click="submitForm"></el-button>
        </el-form-item>
    </el-form>
</template>

<script>
    import {isEmpty, toInteger} from "lodash/lang";
    import Api from "../API/Setting";
    import format from "../data/format/setting";

    export default {
        name: "Setting",
        data: () => ({
            form_disabled: false,
            submit_ing: false,
            form_data: {
                main_port: 0,
                main_http_port: 0,
                allow_lan: false,
                log_level: "",
                auto_update_v2ray: false,
                update_v2ray_proxy: false,
                auto_start: false
            },
            log_level: [
                "debug",
                "info",
                "warning",
                "error",
                "none"
            ]
        }),
        computed: {
            form_rules() {
                return {
                    main_port: [
                        {
                            validator: (rule, value, callback) => {
                                if (isEmpty(value.toString())) {
                                    callback(new Error(this.$t("common.can_not_empty", {
                                        value: this.$t("setting.main_port")
                                    })));
                                    return;
                                }
                                const port = toInteger(value);
                                if (port > 0 && port <= 65535) {
                                    callback();
                                } else {
                                    callback(new Error(this.$t("common.wrong_input", {
                                        value: this.$t("setting.main_port")
                                    })));
                                }
                            },
                            trigger: "blur",
                        }
                    ],
                    main_http_port: [
                        {
                            validator: (rule, value, callback) => {
                                if (isEmpty(value.toString())) {
                                    callback(new Error(this.$t("common.can_not_empty", {
                                        value: this.$t("setting.main_http_port")
                                    })));
                                    return;
                                }
                                const port = toInteger(value);
                                if (port >= 0 && port <= 65535) {
                                    callback();
                                } else {
                                    callback(new Error(this.$t("common.wrong_input", {
                                        value: this.$t("setting.main_http_port")
                                    })));
                                }
                            },
                            trigger: "blur",
                        }
                    ],
                    log_level: {
                        required: true,
                        message: this.$t("common.please_select_as", {value: this.$t("setting.log_level")}),
                        trigger: "blur"
                    }
                }
            }
        },
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
            this.form_disabled = true;
            Api.get()
                .then(data => {
                    this.form_data = data.data;
                    this.form_disabled = false;
                    setTimeout(() => this.is_change = false, 100);
                })
                .catch(window.EMPTY_FUNC)
        },
        methods: {
            toBack() {
                this.$emit("close");
            },
            submitForm() {
                if (this.submit_ing) return;
                if (!this.is_change) {
                    this.toBack();
                    return;
                }
                this.$refs["form"].validate(valid => {
                    if (valid) {
                        this.submit_ing = true;
                        const data = format(this.form_data);
                        Api.save(data)
                            .then(() => this.toBack())
                            .catch(() => this.submit_ing = false);
                    } else {
                        return false;
                    }
                });
            }
        }
    }
</script>
