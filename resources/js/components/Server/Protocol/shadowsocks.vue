<template>
    <div>
        <el-form-item prop="protocol_setting.password" :label="$t('common.password')">
            <el-col :span="5">
                <el-input type="password" :show-password="true" v-model="setting.password"></el-input>
            </el-col>
        </el-form-item>
        <el-form-item prop="protocol_setting.method" :label="$t('server.edit.encrypt_method')">
            <el-select v-model="setting.method" :placeholder="$t('common.please_select')">
                <el-option v-for="item in security_list" :key="item" :label="item" :value="item"></el-option>
            </el-select>
        </el-form-item>
        <el-form-item label="OTA">
            <el-switch v-model="setting.ota"></el-switch>
        </el-form-item>
    </div>
</template>

<script>
    export default {
        name: "ProtocolShadowsocks",
        data: () => ({
            security_list: [
                "aes-128-cfb",
                "chacha20",
                "chacha20-ietf",
                "aes-256-gcm",
                "aes-128-gcm",
                "chacha20-poly1305",
                "chacha20-ietf-poly1305"
            ]
        }),
        props: [
            "setting"
        ]
    }
</script>
