<template>
    <div>
        <el-row type="flex" justify="space-between" align="middle">
            <el-button type="primary" @click="$router.push(`${$route.path}/add`)">
                <font-awesome-icon icon="plus"></font-awesome-icon>
                {{ $t("routing.custom.add_rule") }}
            </el-button>
            <span style="font-size: 0.9rem" v-t="'routing.custom.tip'"></span>
        </el-row>
        <el-row :gutter="10" type="flex" justify="space-between" align="middle" class="vvv-table-top-action">
            <span class="vvv-count" v-t="{path: 'routing.custom.rule_count', args: {count: meta.total}}"></span>
            <div>
                <el-button :disabled="working" size="mini" @click="getIndexList()" v-t="'common.refresh'"></el-button>
                <el-button :disabled="multi_select || working" size="mini" type="success" v-t="'common.enable'"
                           @click="switchButton(null, true)">
                </el-button>
                <el-button :disabled="multi_select || working" size="mini" type="info" v-t="'common.disable'"
                           @click="switchButton(null, false)">
                </el-button>
                <el-button :disabled="multi_select || working" size="mini" type="danger" @click="deleteButton()"
                           v-t="'common.delete'">
                </el-button>
            </div>
        </el-row>
        <el-table :data="items" ref="table" row-key="id" @selection-change="checkBoxSelect">
            <el-table-column type="selection" width="40"></el-table-column>
            <el-table-column :label="$t('common.name')" prop="name" show-overflow-tooltip></el-table-column>
            <el-table-column :formatter="all_to_text" :label="$t('routing.target_port')" prop="port" show-overflow-tooltip></el-table-column>
            <el-table-column :label="$t('routing.target_network')" prop="network"></el-table-column>
            <el-table-column :formatter="all_to_text" :label="$t('routing.target_protocol')" prop="protocol"></el-table-column>
            <el-table-column :label="$t('common.enable')" prop="enable">
                <template slot-scope="scope">
                    <el-switch :disabled="working" :value="scope.row.enable"
                               @change="switchButton(scope.row, !scope.row.enable)">
                    </el-switch>
                </template>
            </el-table-column>
            <el-table-column :label="$t('common.action')">
                <template slot-scope="scope">
                    <el-button :disabled="working" size="mini" type="primary" v-t="'common.edit'"
                               @click="$router.push(`${$route.path}/edit/${scope.row.id}`)">
                    </el-button>
                    <el-popover placement="top" width="160" :ref="`deletePopover-${scope.$index}`">
                        <p v-t="'common.ask_delete'"></p>
                        <div style="text-align: right; margin: 0">
                            <el-button size="mini" type="text" v-t="'common.cancel'"
                                       @click="$refs[`deletePopover-${scope.$index}`].showPopper = false">
                            </el-button>
                            <el-button type="primary" size="mini" v-t="'common.confirm'"
                                       @click="deleteButton(scope.row, $refs[`deletePopover-${scope.$index}`])">
                            </el-button>
                        </div>
                        <el-button :disabled="working" size="mini" type="danger" style="margin-left: 10px"
                                   slot="reference" v-popover:`deletePopover-${scope.$index}` v-t="'common.delete'">
                        </el-button>
                    </el-popover>
                </template>
            </el-table-column>
        </el-table>
        <el-row type="flex" justify="center" class="vvv-pagination">
            <el-pagination background :page-count="meta.last_page" :current-page.sync="meta.current_page"
                           layout="prev, pager, next" hide-on-single-page @current-change="changePage">
            </el-pagination>
        </el-row>
    </div>
</template>

<script>
    import isEmpty from "lodash/isEmpty";
    import join from "lodash/join";
    import toUpper from "lodash/toUpper";
    import meta from "../../data/apiMeta";
    import Api from "../../API/Routing";
    import {deleteItem, getIndexList, switchEnable} from "../../common/index";

    export default {
        name: "RoutingCustom",
        data: () => ({
            meta: meta,
            items: [],
            select_items: [],
            curr_page: 0,
            working: false,
            /* ------------------ */
        }),
        computed: {
            multi_select() {
                return this.select_items.length === 0;
            },
        },
        created() {
            this.getIndexList();
        },
        methods: {
            all_to_text(row, col, value) {
                if (value === "all") {
                    value = this.$i18n.t("common.all");
                }
                return value;
            },
            changePage(page) {
                this.getIndexList(page);
            },
            checkBoxSelect(selection) {
                this.select_items = selection;
            },
            getIndexList(page) {
                getIndexList(page, Api, this, value => {
                    if (isEmpty(value.port)) {
                        value.port = "all";
                    }
                    if (value.protocol.length === 0) {
                        value.protocol = "all";
                    } else {
                        value.protocol = join(value.protocol, ", ");
                    }
                    value.network = toUpper(value.network);
                    return value;
                });
            },
            switchButton(item, enable) {
                switchEnable(item, enable, Api, this);
            },
            deleteButton(item, popover) {
                deleteItem(item, popover, Api, this);
            },
        }
    }
</script>
