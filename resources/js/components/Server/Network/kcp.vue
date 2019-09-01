<template>
    <div>
        <el-form-item prop="network_setting.mtu" :label="`${$t('server.edit.mtu')} (MTU)`">
            <el-col :span="2">
                <el-input type="number" :min="576" :max="1460" v-model="setting.mtu" placeholder="e.g: 1460"></el-input>
            </el-col>
        </el-form-item>
        <el-form-item prop="network_setting.tti" :label="`${$t('server.edit.tti')} (TTI) (ms)`">
            <el-col :span="2">
                <el-input type="number" :min="10" :max="100" v-model="setting.tti" placeholder="e.g: 50"></el-input>
            </el-col>
        </el-form-item>
        <el-form-item :label="`${$t('server.edit.uplink_capacity')} (MB/s)`">
            <el-col :span="2">
                <el-input type="number" v-model="setting.uplinkCapacity"></el-input>
            </el-col>
        </el-form-item>
        <el-form-item :label="`${$t('server.edit.downlink_capacity')} (MB/s)`">
            <el-col :span="2">
                <el-input type="number" v-model="setting.downlinkCapacity"></el-input>
            </el-col>
        </el-form-item>
        <el-form-item :label="$t('server.edit.congestion')">
            <el-switch v-model="setting.congestion"></el-switch>
        </el-form-item>
        <el-form-item :label="`${$t('server.edit.read_buffer_size')} (MB)`">
            <el-col :span="2">
                <el-input type="number" v-model="setting.readBufferSize"></el-input>
            </el-col>
        </el-form-item>
        <el-form-item :label="`${$t('server.edit.write_buffer_size')} (MB)`">
            <el-col :span="2">
                <el-input type="number" v-model="setting.writeBufferSize"></el-input>
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
        name: "NetworkKcp",
        data: () => ({
            headers: udpHeaderType
        }),
        props: [
            "setting"
        ]
    }
</script>
