import {Loading} from "element-ui";
import isFunction from "lodash/isFunction";

export default (Api, _this, format, callback) => {
    const loading = Loading.service({
        fullscreen: true,
        text: _this.$t("common.loading"),
    });
    _this.submit_ing = !_this.submit_ing;
    _this.is_ready = !_this.is_ready;
    Api.get(_this.id)
        .then(data => {
            if (isFunction(format)) {
                data.data = format(data.data);
            }
            _this.form_data = data.data;
            _this.submit_ing = !_this.submit_ing;
            setTimeout(() => _this.is_change = false, 100);
            if (isFunction(callback)) {
                callback();
            }
        })
        .catch(() => {
            _this.form_disabled = true;
            setTimeout(_this.toBack, 3000);
        })
        .finally(() => loading.close());
}
