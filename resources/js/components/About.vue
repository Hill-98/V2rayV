<template>
    <div id="vvv-about">
        <p v-html="$t('tips.about')"></p>
        <fieldset class="vvv-version">
            <legend>Version</legend>
            <p>V2rayV: {{ version.vvv }}</p>
            <p>V2ray Core: {{ version.v2ray }}</p>
        </fieldset>
        <el-button class="vvv-check-update" type="primary" :disabled="check_ing"
                   @click="checkUpdate()">
            Check Update
        </el-button>
    </div>
</template>

<script>
    import {Message} from "element-ui";
    import Api from "../API/Version";

    export default {
        name: "About",
        data: () => ({
            check_ing: false,
            version: {
                vvv: "0",
                v2ray: "0"
            }
        }),
        created() {
            Api.show()
                .then(data => {
                    this.version = data.data;
                })
                .catch(window.EMPTY_FUNC);
        },
        methods: {
            checkUpdate() {
                this.check_ing = true;
                Api.check()
                    .then(data => {
                        if (data.data.vvv.is_update === false && data.data.v2ray.is_update === false) {
                            Message.info(this.$t("about.not_update"));
                        } else {
                            if (data.data.v2ray.is_update) {
                                Message.info(this.$t("about.v2ray_update", {version: data.data.v2ray.new_version}))
                            }
                            Api.checkVVV(data);
                        }

                    })
                    .catch(window.EMPTY_FUNC)
                    .finally(() => this.check_ing = false);
            }
        }
    }
</script>
