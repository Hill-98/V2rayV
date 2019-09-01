<template>
    <div>
        <el-form-item :label="`${$t('server.edit.mux')} (MUX)`">
            <el-switch v-model="setting.enabled"></el-switch>
            <el-tooltip effect="dark" placement="top">
                <div slot="content" v-html="$t('tips.server.mux')"></div>
                <el-button class="vvv-question-btn" size="mini" circle>
                    <font-awesome-icon class="vvv-menu-icon" icon="question"></font-awesome-icon>
                </el-button>
            </el-tooltip>
        </el-form-item>
        <el-form-item v-if="setting.enabled" prop="mux.concurrency" :rules="rules.mux.concurrency"
                      :label="$t('server.edit.mux_concurrency')">
            <el-col :span="2">
                <el-input type="number" :min="1" :max="1024" v-model="setting.concurrency" placeholder="e.g: 8">
                </el-input>
            </el-col>
        </el-form-item>
    </div>
</template>

<script>
    import isEmpty from "lodash/isEmpty";
    import toInteger from "lodash/toInteger";

    export default {
        name: "Mux",
        computed: {
            rules() {
                return {
                    mux: {
                        concurrency: [
                            {
                                validator: (rule, value, callback) => {
                                    if (isEmpty(value.toString())) {
                                        callback(new Error(this.$t('common.can_not_empty', {
                                            value: this.$t("server.edit.mux_concurrency")
                                        })));
                                        return;
                                    }
                                    value = toInteger(value);
                                    if (value >= 1 && value <= 1024) {
                                        callback();
                                    } else {
                                        callback(new Error(this.$t('common.wrong_input', {
                                            value: this.$t("server.edit.mux_concurrency")
                                        })));
                                    }
                                },
                                trigger: "blur"
                            }
                        ]
                    }
                }
            }
        },
        props: [
            "setting"
        ]
    }
</script>
