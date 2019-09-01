import {Loading} from "element-ui";
import {isFunction, isInteger, isUndefined} from "lodash/lang";
import map from "lodash/map";

export default (page, Api, _this, format) => {
    if (isUndefined(page) || !isInteger(page)) page = _this.meta.current_page;
    const loading = Loading.service({
        fullscreen: true,
        text: _this.$t("common.loading")
    });
    Api.index(page)
        .then((data => {
            _this.meta = data.meta;
            if (isFunction(format)) {
                data.data = map(data.data, format)
            }
            _this.items = data.data;
            _this.select_items = [];
            _this.curr_page = data.meta.current_page;
            scroll(0, 0)
        }))
        .catch(() => {
            if (_this.meta.current_page !== 0) {
                _this.$set(_this.meta, "current_page", _this.curr_page);
            }
        })
        .finally(() => loading.close());
}
