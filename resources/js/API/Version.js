import client from "../client";
import toInteger from "lodash/toInteger";
import {MessageBox} from "element-ui";
import {i18n} from "../i18n";

class Version {
    static prefix = "version";

    static show() {
        return client("get", this.prefix);
    }

    static check(only_vvv) {
        return client("get", `${this.prefix}/check?check=${toInteger(only_vvv)}`)
    }

    static checkVVV(data) {
        if (data.data.vvv.is_update) {
            MessageBox.confirm(i18n.t("about.vvv_update", {version: data.data.vvv.new_version}), {
                cancelButtonText: i18n.t("common.no"),
                confirmButtonText: i18n.t("common.yes")
            })
                .then(() => window.open("https://github.com"))
                .catch(window.EMPTY_FUNC);
        }
    }
}

export default Version;
