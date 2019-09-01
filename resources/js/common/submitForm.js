import cloneDeep from "lodash/cloneDeep";
import isFunction from "lodash/isFunction";

export default (Api, _this, format, callback) => {
    if (_this.submit_ing) return;
    if (!_this.is_change) {
        _this.toBack();
        return;
    }
    _this.$refs["form"].validate(valid => {
        if (valid) {
            _this.submit_ing = true;
            let data = cloneDeep(_this.form_data);
            if (isFunction(format)) {
                data = format(data);
            }
            Api.save(data, _this.id)
                .then(() => {
                    if (isFunction(callback)) {
                        callback();
                    } else {
                        _this.is_change = false;
                        _this.toBack();
                    }
                })
                .catch(() => _this.submit_ing = false);
        } else {
            return false;
        }
    });
}
