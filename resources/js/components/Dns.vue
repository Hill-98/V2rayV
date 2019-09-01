<template>
    <div>
        <p v-t="'dns.title'"></p>
        <el-button type="text" v-t="'dns.view_example'" @click="view_example"></el-button>
        <el-form style="margin-top: 10px">
            <el-form-item>
                <el-input ref="text" type="textarea" :rows="30" v-model="text_data"></el-input>
            </el-form-item>
            <el-form-item>
                <el-row type="flex" justify="end">
                    <el-button :disabled="submit_ing" type="primary" v-t="'common.save'" @click="submitForm"></el-button>
                </el-row>
            </el-form-item>
        </el-form>
    </div>
</template>

<script>
    import {Loading, MessageBox, Message} from "element-ui";
    import Api from "../API/Dns";

    export default {
        name: "dns",
        data: () => ({
            text_data: "",
            is_change: false,
            submit_ing: false
        }),
        watch: {
            text_data() {
                if (!this.is_change) {
                    this.is_change = true;
                }
            }
        },
        created() {
            window.addEventListener("beforeunload", this.beforeunload);
            const loading = Loading.service({
                fullscreen: true,
                text: this.$t("common.loading"),
            });
            this.submit_ing = true;
            Api.get()
                .then(data => {
                    this.text_data = data.data;
                    this.submit_ing = false;
                    setTimeout(() => this.is_change = false, 100)
                })
                .catch(window.EMPTY_FUNC)
                .finally(() => {
                    loading.close();
                    this.$refs["text"].focus();
                });
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
            view_example() {
                MessageBox({
                    title: this.$i18n.t("dns.view_example"),
                    message: this.$i18n.t("tips.dns.view_example"),
                    confirmButtonText: this.$i18n.t("common.i_know"),
                    dangerouslyUseHTMLString: true,
                })
            },
            submitForm() {
                if (this.submit_ing || !this.is_change) return;
                this.submit_ing = true;
                Api.put(this.text_data)
                    .then(() => {
                        Message.success(this.$t("common.save_success"));
                        this.is_change = false;
                    })
                    .catch(window.EMPTY_FUNC)
                    .finally(() => this.submit_ing = false);
            }
        }
    }
</script>
