<template>
    <div>
        <el-button type="primary" @click="$router.push(`${$route.path}/add`)">
            <font-awesome-icon icon="plus"></font-awesome-icon>
            {{ $t("subscribe.index.add_subscribe") }}
        </el-button>
        <el-row :gutter="10" type="flex" justify="space-between" align="middle" class="vvv-table-top-action">
            <span class="vvv-count" v-t="{path: 'subscribe.index.subscribe_count', args: {count: meta.total}}"></span>
            <div>
                <el-button :disabled="working" size="mini" @click="getIndexList()" v-t="'common.refresh'"></el-button>
                <el-button :disabled="multi_select || working" size="mini" type="danger" @click="deleteButton()"
                           v-t="'common.delete'">
                </el-button>
            </div>
        </el-row>
        <el-table :data="items" @selection-change="checkBoxSelect" ref="table" row-key="id">
            <el-table-column type="selection" width="40"></el-table-column>
            <el-table-column :label="$t('common.name')" prop="name"></el-table-column>
            <el-table-column :formatter="formatterIsEncrypt" :label="$t('subscribe.index.encrypt')" prop="is_encrypt"></el-table-column>
            <el-table-column :label="$t('subscribe.index.server_count')" prop="server_count"></el-table-column>
            <el-table-column :label="$t('subscribe.index.last_update_status')" prop="last_success">
                <template slot-scope="scope">
                    <el-tag type="info" v-if="!scope.row.last_success && scope.row.update_at === null" v-t="'subscribe.index.not_update'"></el-tag>
                    <el-tag type="success" v-else-if="scope.row.last_success" v-t="'common.success'"></el-tag>
                    <el-tag type="danger" v-else v-t="'common.fail'"></el-tag>
                </template>
            </el-table-column>
            <el-table-column :formatter="formatterUpdateTime" :label="$t('subscribe.index.last_update_time')" prop="update_at"></el-table-column>
            <el-table-column :label="$t('common.action')">
                <template slot-scope="scope">
                    <el-button :disabled="working" size="mini" type="primary" v-t="'common.edit'"
                               @click="$router.push(`${$route.path}/edit/${scope.row.id}`)">
                    </el-button>
                    <el-button :disabled="working" size="mini" type="success" v-t="'common.update'"
                               @click="subscribeUpdate(scope.row.id)">
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
    import meta from "../../data/apiMeta";
    import {Notification} from "element-ui";
    import Api from "../../API/Subscribe";
    import {deleteItem, getIndexList} from "../../common";

    export default {
        name: "SubscribeIndex",
        data: () => ({
            meta: meta,
            items: [],
            select_items: [],
            curr_page: 0,
            working: false
        }),
        computed: {
            multi_select() {
                return this.select_items.length === 0;
            }
        },
        created() {
            this.getIndexList();
        },
        methods: {
            checkBoxSelect(selection) {
                this.select_items = selection;
            },
            changePage(page) {
                this.getIndexList(page);
            },
            formatterIsEncrypt(row, col, value) {
                return value ? this.$i18n.t("common.yes") : this.$i18n.t("common.no");
            },
            formatterUpdateTime(row, col, value) {
                if (value === null) {
                    return "";
                }
                return new Date(value * 1000).toLocaleString();
            },
            getIndexList(page) {
                getIndexList(page, Api, this);
            },
            deleteButton(item, popover) {
                deleteItem(item, popover, Api, this);
            },
            subscribeUpdate(id) {
                Api.subscribeUpdate(id)
                    .then(() => Notification.success(this.$i18n.t("subscribe.index.subscribe_update")))
                    .catch(window.EMPTY_FUNC);
            }
        }
    }
</script>
