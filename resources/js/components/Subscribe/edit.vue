<template>
    <div>
        <el-page-header @back="toBack" :content="action_text">
            <template slot="title">
                <span v-t="'el.pageHeader.title'"></span>
            </template>
        </el-page-header>
        <el-form :disabled="form_disabled" :model="form_data" :rules="form_rules" ref="form" class="vvv-form"
                 label-width="150px">
            <el-form-item prop="name" :label="$t('common.name')">
                <el-col :span="6">
                    <el-input v-model="form_data.name"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item prop="url" :label="$t('subscribe.edit.subscribe_url')">
                <el-col :span="6">
                    <el-input v-model="form_data.url"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item :label="$t('common.password')">
                <el-col :span="6">
                    <el-input type="password" v-model="form_data.password"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item :label="$t('subscribe.edit.auto_update')">
                <el-switch v-model="form_data.auto_update"></el-switch>
            </el-form-item>
            <el-form-item :label="$t('subscribe.edit.proxy_update')">
                <el-switch v-model="form_data.proxy_update"></el-switch>
            </el-form-item>
            <mux :setting="form_data.mux"></mux>
            <el-form-item>
                <el-button :disabled="submit_ing" type="primary" v-t="'common.save'" @click="submitForm"></el-button>
            </el-form-item>
        </el-form>
    </div>
</template>

<script>
    import {endsWith, snakeCase} from "lodash/string";
    import {MessageBox} from "element-ui";
    import mux from "../Server/mux";
    import Api from "../../API/Subscribe";
    import format from "../../data/format/subscribe";
    import {editInit, submitForm} from "../../common/index";

    export default {
        name: "SubscribeEdit",
        data: () => ({
            form_disabled: false,
            is_change: false,
            submit_ing: false,
            form_data: {
                name: "",
                url: "",
                password: "",
                auto_update: false,
                proxy_update: false,
                mux: {
                    enabled: false,
                    concurrency: 8
                },
            },
        }),
        computed: {
            action_text() {
                return this.$t(`title.${snakeCase(this.$route.name)}`);
            },
            form_rules() {
                return {
                    name: {
                        required: true,
                        message: this.$t("common.can_not_empty", {value: this.$i18n.t("common.name")}),
                        trigger: "blur"
                    },
                    url: {
                        required: true,
                        message: this.$t("common.can_not_empty", {value: this.$i18n.t("subscribe.edit.subscribe_url")}),
                        trigger: "blur"
                    }
                };
            }
        },
        components: {
            mux
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
            }
        }
    }
</script>
