<template>
    <div>
        <el-form-item prop="network_setting.security" :label="$t('server.edit.encrypt_method')">
            <el-select v-model="setting.security" :placeholder="$t('common.please_select')">
                <el-option v-for="item in security_list" :key="item" :label="item" :value="item"></el-option>
            </el-select>
        </el-form-item>
        <el-form-item v-if="setting.security !== 'none'" prop="network_setting.key" :label="$t('server.edit.key')">
            <el-col :span="5">
                <el-input type="password" :show-password="true" v-model="setting.key"></el-input>
            </el-col>
        </el-form-item>
        <el-form-item prop="network_setting.header.type" :label="$t('server.edit.header_type')">
            <el-select v-model="setting.header.type" :placeholder="$t('common.please_select')">
                <el-option class="vvv-custom-option" v-for="item in headers" :key="item.value"
                           :label="$t(`server.edit.udp_header_type.${item.label}`)" :value="item.value">
                    <span v-t="`server.edit.udp_header_type.${item.label}`"></span>
                    <span>{{ item.value }}</span>
                </el-option>
            </el-select>
        </el-form-item>
    </div>
</template>

<script>
    import udpHeaderType from "../../../data/server/udpHeaderType";

    export default {
        name: "NetworkQuic",
        data: () => ({
            security_list: [
                "none",
                "aes-128-gcm",
                "chacha20-poly1305"
            ],
            headers: udpHeaderType
        }),
        props: [
            "setting"
        ],
    }
</script>

<style>

</style>
