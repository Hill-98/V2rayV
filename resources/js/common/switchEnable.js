import {filter, includes, map} from "lodash/collection";
import isObject from "lodash/isObject";

export default (item, enable, api, _this) => {
    if (isObject(item)) {
        item = [item];
    } else {
        item = _this.select_items;
    }
    // 过滤掉当前状态和目标状态一致的条目
    const id = map(filter(item, value => {
        return value.enable !== enable
    }), "id");
    if (id.length === 0) return;
    _this.working = true;
    api.switchEnable(id, enable)
        .then(data => {
            for (const item of item) {
                if (includes(data.data, item.id)) {
                    item.enable = enable;
                }
            }
        })
        .catch(window.EMPTY_FUNC)
        .finally(() => _this.working = false);
}
