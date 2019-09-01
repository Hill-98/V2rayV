<template>
    <div>
        <el-form-item prop="protocol_setting.id" :label="$t('server.edit.user_id')">
            <el-col :span="5">
                <el-input v-model="setting.id" placeholder="e.g: 13152fae-d25a-4d78-b318-74397eb08184"></el-input>
            </el-col>
            <el-col :span="2" style="margin-left: 10px">
                <el-button @click="GenerateUUID" v-t="'common.generate'"></el-button>
            </el-col>
        </el-form-item>
        <el-form-item :label="$t('server.edit.alter_id')">
            <el-col :span="2">
                <el-input type="number" :min="0" :max="65535" v-model="setting.alterId"></el-input>
            </el-col>
        </el-form-item>
        <el-form-item prop="protocol_setting.security" :label="$t('server.edit.encrypt_method')">
            <el-select v-model="setting.security" :placeholder="$t('common.please_select')">
                <el-option v-for="item in security_list" :key="item" :label="item" :value="item"></el-option>
            </el-select>
        </el-form-item>
    </div>
</template>

<script>
    import uuid from "uuid/v4";

    export default {
        name: "ProtocolVmess",
        data: () => ({
            security_list: [
                "aes-128-gcm",
                "chacha20-poly1305",
                "auto",
                "none"
            ]
        }),
        props: [
            "setting"
        ],
        methods: {
            GenerateUUID() {
                this.$set(this.setting, "id", uuid());
            }
        }
    }
</script>
