<template>
    <div>
        <el-form-item :label="$t('common.username')">
            <el-col :span="5">
                <el-input v-model="setting.user"></el-input>
            </el-col>
        </el-form-item>
        <el-form-item :label="$t('common.password')">
            <el-col :span="5">
                <el-input type="password" :show-password="true" v-model="setting.pass"></el-input>
            </el-col>
        </el-form-item>
    </div>
</template>

<script>
    export default {
        name: "ProtocolSocks",
        props: [
            "setting"
        ]
    }
</script>
