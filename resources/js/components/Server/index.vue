<template>
    <div>
        <el-dropdown @command="importUrlDialog = true" split-button type="primary"
                     @click="$router.push(`${$route.path}/add`)">
            <font-awesome-icon icon="plus"></font-awesome-icon>
            {{ $t("server.index.add_server") }}
            <el-dropdown-menu slot="dropdown">
                <el-dropdown-item v-t="'server.index.import_url'"></el-dropdown-item>
            </el-dropdown-menu>
        </el-dropdown>
        <el-row :gutter="10" type="flex" justify="space-between" align="middle" class="vvv-table-top-action">
            <span class="vvv-count" v-t="{path: 'server.index.server_count', args: {count: meta.total}}"></span>
            <div>
                <el-button :disabled="working" size="mini" @click="getIndexList()">{{ $t("common.refresh") }}</el-button>
                <el-button :disabled="multi_select || working" size="mini" type="success" v-t="'common.enable'"
                           @click="switchButton(null, true)">
                </el-button>
                <el-button :disabled="multi_select || working" size="mini" type="info" v-t="'common.disable'"
                           @click="switchButton(null, false)">
                </el-button>
                <el-button :disabled="multi_select || working" size="mini" type="danger" v-t="'common.delete'"
                           @click="deleteButton()">
                </el-button>
                <el-dropdown @command="moreButton" style="margin-left: 10px" size="mini">
                    <el-button :disabled="multi_select || working" size="mini">
                        {{ $t("common.more") }}
                        <i class="el-icon-arrow-down el-icon--right"></i></el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item command="share">{{ $t("common.share") }}</el-dropdown-item>
                        <el-dropdown-item command="export-client"
                                          v-t="{path: 'server.index.export_as', args: {value: $t('common.client')}}">
                        </el-dropdown-item>
                        <el-dropdown-item command="export-server"
                                          v-t="{path: 'server.index.export_as', args: {value: $t('common.server')}}">
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
        </el-row>
        <el-table :data="items" ref="table" row-key="id" @selection-change="checkBoxSelect">
            <el-table-column type="selection" width="40"></el-table-column>
            <el-table-column :label="$t('common.name')" prop="name" show-overflow-tooltip>
                <template slot-scope="scope">
                    <span>
                        {{ scope.row.name }}
                    </span>
                    <el-tag style="margin-left: 10px" size="small" v-if="main_server === scope.row.id"
                            v-t="'server.index.main_server'">
                    </el-tag>
                </template>
            </el-table-column>
            <el-table-column :label="$t('common.address')" prop="address" show-overflow-tooltip>
                <template slot-scope="scope">
                    <span :title="$t('server.index.address_dblclick')" @dblclick="toggleAddress(scope)">
                        {{ scope.row.showAddress ? scope.row.address : formatterAddress(scope.row.address) }}
                    </span>
                </template>
            </el-table-column>
            <el-table-column :label="$t('common.port')" prop="port" width="70"></el-table-column>
            <el-table-column :label="$t('server.local_port')" prop="local_port" width="100"></el-table-column>
            <el-table-column :label="$t('common.protocol')" prop="protocol" width="110"></el-table-column>
            <el-table-column :label="$t('common.group')" prop="subscribe.name" width="100" show-overflow-tooltip>
            </el-table-column>
            <el-table-column :label="$t('common.enable')" prop="enable" width="100">
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
                    <el-dropdown @command="moreButton" style="margin-left: 10px" size="mini">
                        <el-button :disabled="working" size="mini">
                            {{ $t("common.more") }}
                            <i class="el-icon-arrow-down el-icon--right"></i>
                        </el-button>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item :command="`share:${scope.row.id}`" v-t="'common.share'">
                            </el-dropdown-item>
                            <!--                            <el-dropdown-item :command="`share:${scope.row.id}`">测试速度</el-dropdown-item>-->
                            <el-dropdown-item :command="`ping:${scope.row.id}`" v-t="'server.index.test_delay'">
                            </el-dropdown-item>
                            <el-dropdown-item :command="`export-client:${scope.row.id}`"
                                              v-t="{path: 'server.index.export_as', args: {value: $t('common.client')}}"
                            ></el-dropdown-item>
                            <el-dropdown-item :command="`export-server:${scope.row.id}`"
                                              v-t="{path: 'server.index.export_as', args: {value: $t('common.server')}}"
                            ></el-dropdown-item>
                            <el-dropdown-item :command="`set-main-server:${scope.row.id}`"
                                              v-t="'server.index.set_main_server'"
                            ></el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </template>
            </el-table-column>
        </el-table>
        <el-row type="flex" justify="center" class="vvv-pagination">
            <el-pagination background :page-count="meta.last_page" :current-page.sync="meta.current_page"
                           layout="prev, pager, next" hide-on-single-page @current-change="changePage">
            </el-pagination>
        </el-row>
        <el-dialog :title="$t('server.index.import_url')" :visible.sync="importUrlDialog">
            <el-form>
                <el-form-item>
                    <el-input type="textarea" :rows="10" :placeholder="$t('server.index.paste_url')"
                              v-model="importShareUrl.value">
                    </el-input>
                </el-form-item>
                <el-form-item :label="$t('common.password')">
                    <el-col :span="6">
                        <el-input type="password" :show-password="true" v-model="importShareUrl.password"></el-input>
                    </el-col>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="addServerFromURL()" v-t="'common.import'"></el-button>
            </span>
        </el-dialog>
        <el-dialog :title="$t('server.index.share_url')" :visible.sync="shareUrlDialog">
            <p style="color: #00BCD4; margin: 0" v-t="'tips.server.share_need'">
            </p>
            <el-form>
                <el-form-item label="V2rayV">
                    <el-input type="textarea" ref="share-url-vvv" @focus="$refs['share-url-vvv'].select()" :rows="5"
                              readonly :value="shareUrl.vvv">
                    </el-input>
                </el-form-item>
                <el-form-item label="V2rayN">
                    <el-input type="textarea" ref="share-url-vn" @focus="$refs['share-url-vn'].select()" :rows="5"
                              readonly :value="shareUrl.V2rayN">
                    </el-input>
                </el-form-item>
                <el-form-item v-if="shareUrl.password.length !== 0" :label="$t('common.password')">
                    <el-col :span="6">
                        <el-input readonly :value="shareUrl.password"></el-input>
                    </el-col>
                </el-form-item>
            </el-form>
        </el-dialog>
        <el-dialog
            :title="export_config.title"
            :visible.sync="exportDialog">
            <el-form>
                <el-form-item label="config.json">
                    <el-input type="textarea" :rows="15" readonly :value="export_config.text"></el-input>
                </el-form-item>
                <el-row type="flex" justify="center">
                    <el-link type="primary" v-t="'server.index.save_local'" @click="saveLocalForIE"
                             :underline="false" :href="export_config.url" target="_blank" download="config.json">
                    </el-link>
                </el-row>
            </el-form>
        </el-dialog>
    </div>
</template>

<script>
    import get from "lodash/get";
    import join from "lodash/join";
    import {isEmpty, isFunction, isObject, isUndefined, toInteger} from "lodash/lang";
    import map from "lodash/map";
    import {camelCase, split} from "lodash/string";
    import {Loading, Message, MessageBox} from "element-ui";
    import meta from "../../data/apiMeta";
    import Api from "../../API/Server";
    import settingApi from "../../API/Setting";
    import {deleteItem, getIndexList, switchEnable} from "../../common/index";


    export default {
        name: "Server",
        data: () => ({
            meta: meta,
            items: [],
            select_items: [],
            curr_page: 0,
            working: false,
            /* ------------------ */
            importUrlDialog: false,
            shareUrlDialog: false,
            exportDialog: false,
            importShareUrl: {
                value: "",
                password: ""
            },
            shareUrl: {
                vvv: [],
                V2rayN: [],
                password: ""
            },
            export_config: {
                title: "",
                text: "",
                url: ""
            },
            main_server: 0
        }),
        computed: {
            multi_select() {
                return this.select_items.length === 0;
            }
        },
        created() {
            this.getIndexList();
            settingApi.getMainServer()
                .then(data => this.main_server = data.data.id)
                .catch(window.EMPTY_FUNC)
        },
        methods: {
            formatterAddress(cellValue) {
                let reserved = cellValue.slice(0, Math.floor(cellValue.length / 3));
                for (let i = 0; i < cellValue.length - reserved.length; i++) {
                    reserved += "*";
                }
                return reserved;
            },
            checkBoxSelect(selection) {
                this.select_items = selection;
            },
            // 切换地址显示
            toggleAddress(scope) {
                this.$set(scope.row, "showAddress", !scope.row.showAddress);
            },
            changePage(page) {
                this.getIndexList(page);
            },
            moreButton(e) {
                e = split(e, ":");
                const action = `${camelCase(e[0])}Button`;
                const func = get(this, action);
                if (!isFunction(this[action])) return;
                let param;
                if (e.length === 2) {
                    param = toInteger(e[1]);
                } else {
                    param = map(this.select_items, "id");
                }
                func(param);
            },
            getIndexList(page) {
                getIndexList(page, Api, this, value => {
                    value.showAddress = false;
                    return value;
                });
            },
            addServerFromURL() {
                const value = this.importShareUrl.value;
                const password = this.importShareUrl.password;
                this.importUrlDialog = false;
                this.importShareUrl = {
                    value: "",
                    password: ""
                };
                if (isEmpty(value)) return;
                Api.importShareUrl(value, password)
                    .then(data => {
                        const new_count = get(data.data, "new", 0);
                        const fail_count = get(data.data, "fail", 0);
                        const total = get(data.data, "total", 0);
                        switch (true) {
                            case total === fail_count: {
                                let text = this.$t("server.index.import_fail");
                                if (isEmpty(password)) {
                                    text += this.$t("server.index.import_fail_password")
                                }
                                Message.error(text);
                                break;
                            }
                            case fail_count !== 0:
                                Message.warning(this.$t("server.index.import_partial_success", {
                                    new: new_count,
                                    fail: fail_count
                                }));
                                break;
                            case fail_count === 0:
                                Message.success(this.$t("server.index.import_success", {new: new_count}));
                                break;
                        }
                        if (new_count !== 0 && (this.meta.total <= this.meta.per_page)) {
                            this.getIndexList()
                        }
                    })
                    .catch(window.EMPTY_FUNC);
            },
            switchButton(items, enable) {
                switchEnable(items, enable, Api, this);
            },
            exportConfig(id, type) {
                Api.exportConfig(id, type)
                    .then(data => {
                        if (isObject(data.data)) {
                            let value;
                            if (type === "client") {
                                value = this.$t("common.client");
                            } else if (type === "server") {
                                value = this.$t("common.server");
                            } else {
                                return;
                            }
                            const title = this.$t("server.index.export_as", {value: value});
                            if (!isEmpty(this.export_config.url) && isUndefined(navigator.msSaveBlob)) {
                                URL.revokeObjectURL(this.export_config.url);
                            }
                            const text = JSON.stringify(data.data, null, 4);
                            let url;
                            const blob = new Blob([text], {type: "application/json"});
                            if (navigator.msSaveBlob) {
                                window.vvv = window.vvv || {};
                                window.vvv.export_config_blob = blob;
                            } else {
                                url = URL.createObjectURL(blob);
                            }
                            this.export_config = {
                                title: title,
                                text: text,
                                url: url
                            };
                            this.exportDialog = true;
                        }
                    })
                    .catch(window.EMPTY_FUNC);
            },
            saveLocalForIE() {
                if (window.navigator.msSaveBlob) {
                    window.navigator.msSaveBlob(window.vvv.export_config_blob, 'config.json')
                }
            },
            deleteButton(item, popover) {
                deleteItem(item, popover, Api, this);
            },
            shareButton(id) {
                let encrypt = true;
                MessageBox.confirm(this.$t("server.index.ask_encrypt"), this.$t("common.share"), {
                    confirmButtonText: this.$t("common.yes"),
                    cancelButtonText: this.$t("common.no"),
                    distinguishCancelAndClose: true,
                    callback: action => {
                        if (action === "cancel") {
                            encrypt = false
                        }
                        if (action !== "close") {
                            Api.exportShareUrl(id, encrypt)
                                .then(data => {
                                    this.shareUrlDialog = true;
                                    this.shareUrl.vvv = join(get(data.data, "vvv"), "\n");
                                    this.shareUrl.V2rayN = join(get(data.data, "V2rayN"), "\n");
                                    if (encrypt) {
                                        this.shareUrl.password = get(data.data, "password");
                                    } else {
                                        this.shareUrl.password = "";
                                    }
                                })
                                .catch(window.EMPTY_FUNC);
                        }
                    }
                });
            },
            pingButton(id) {
                this.working = true;
                const loading = Loading.service({
                    fullscreen: true,
                    spinner: "el-icon-loading",
                    text: this.$t("server.index.testing")
                });
                Api.ping(id)
                    .then(data => {
                        if (data.data.length === 0) {
                            Message.error(this.$t("server.index.test_fail"));
                        } else {
                            const style = document.createElement('style');
                            style.type = 'text/css';
                            style.innerHTML = ".vvv-message-box {width: 500px;}";
                            document.head.appendChild(style);
                            MessageBox({
                                message: join(data.data, "<br>"),
                                title: this.$t("server.index.test_result"),
                                customClass: "vvv-message-box",
                                dangerouslyUseHTMLString: true,
                                callback: () => setTimeout(() => style.remove(), 2000)
                            });
                        }
                    })
                    .catch(window.EMPTY_FUNC)
                    .finally(() => {
                        loading.close();
                        this.working = false;
                    });
            },
            exportClientButton(id) {
                this.exportConfig(id, "client");
            },
            exportServerButton(id) {
                this.exportConfig(id, "server");
            },
            setMainServerButton(id) {
                this.working = true;
                settingApi.setMainServer(id)
                    .then(() => {
                        Message.success(this.$t("common.success"));
                        this.main_server = toInteger(id);
                    })
                    .catch(window.EMPTY_FUNC)
                    .finally(() => this.working = false);
            }
        }
    }
</script>
