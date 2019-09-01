<template>
    <div>
        <el-form-item prop="network_setting.host" :label="$t('common.domain_name')">
            <el-col :span="6">
                <el-input v-model="setting.host" :placeholder="`e.g: www.example.com ${$t('common.value_split')}`">
                </el-input>
            </el-col>
        </el-form-item>
        <el-form-item prop="network_setting.path" :label="$t('common.path')">
            <el-col :span="6">
                <el-input type="textarea" :autosize="true" v-model="setting.path" placeholder="e.g: /example">
                </el-input>
            </el-col>
        </el-form-item>
    </div>
</template>

<script>
    export default {
        name: "NetworkHttp",
        props: [
            "setting"
        ],
    }
</script>
