import {Message} from "element-ui";
import {i18n} from "../i18n";
import has from "lodash/has";

export default function (error) {
    let error_text;
    if (error.response) {
        if (has(error.response.data, "message")) {
            error_text = error.response.data.message;
        } else {
            error_text = i18n.t("error.network.response");
        }
    } else {
        error_text = i18n.t("error.network.request");
    }
    Message.error({
        message: i18n.t("error.network.common", {error: error_text})
    });
    return Promise.reject(error);
}
