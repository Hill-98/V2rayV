import difference from "lodash/difference";
import {MessageBox} from "element-ui";
import isUndefined from "lodash/isUndefined";
import forEach from "lodash/forEach";

export default (item, popover, api, _this) => {
    let delete_items = [];
    // 判断是删除单个还是多个
    if (item) {
        delete_items.push(item);
    } else {
        delete_items = _this.select_items;
    }
    const delete_func = () => {
        _this.working = true;
        const success_list = [];
        let x = 0;
        const d = () => {
            x++;
            if (x < delete_items.length) return;

            _this.items = difference(_this.items, success_list);
            _this.select_items = [];
            _this.$refs["table"].clearSelection();
            _this.$set(_this.meta, "total", _this.meta.total - success_list.length);
            // 判断此页的服务器是否被清空了
            if (_this.items.length === 0) {
                let page = _this.meta.current_page;
                // 如果此页是最后一页，应该加载前一页。
                if (_this.meta.current_page === _this.meta.last_page) {
                    page--;
                }
                if (page > 0) _this.getIndexList(page);
            }
            _this.$set(_this.meta, "last_page", Math.round(_this.meta.total / _this.meta.per_page));
            _this.working = false;
        };
        forEach(delete_items,  item => {
            api.delete(item.id)
                .then(() => success_list.push(item))
                .catch(window.EMPTY_FUNC)
                .finally(d);
        });
    };
    if (isUndefined(popover)) {
        MessageBox.confirm("你确定要删除吗？", "删除")
            .then(() =>  delete_func())
            .catch(window.EMPTY_FUNC);
    } else {
        popover.showPopper = false;
        delete_func()
    }
}
