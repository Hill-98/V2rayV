import {Message} from "element-ui";
import {isFunction, isUndefined, toInteger} from "lodash/lang";
import {get, has} from "lodash/object";
import {i18n} from "../i18n";

const ErrorMessage = {
    "103": {},
    "401": {},
    "402": {},
    "901": {
        text(target, data) {
            const key = get(data, "key");
            if (!isUndefined(key) && has(ValidationKey, `${target}.${key}`)) {
                const key = data.key;
                const key_text = i18n.t(get(ValidationKey, `${target}.${key}`));
                const status = data.status;
                switch (status) {
                    case "empty":
                        return i18n.t("error.api.901.empty", {value: key_text});
                    case "invalid":
                        return i18n.t("error.api.901.invalid", {value: key_text});
                }
            } else {
                return i18n.t("error.api.901.unknown");
            }

        }
    },
    "902": {},
    "903": {},
    "904": {},
    "905": {},
    "911": {}
};

const ErrorName = {
    server: "error.api.name.server",
    routing: "error.api.name.routing",
    subscribe: "error.api.name.subscribe",
    setting: "error.api.name.setting"
};

const ValidationKey = {
    setting: {
        main_port: "setting.main_port",
        main_http_port: "setting.main_http_port",
        allow_lan: "setting.allow_lan",
        log_level: "setting.log_level",
        auto_update_v2ray: "setting.auto_update_v2ray",
        update_v2ray_proxy: "setting.update_v2ray_proxy",
        auto_start: "setting.auto_start"
    },
    server: {
        address: "server.edit.address",
        port: "server.edit.port",
        protocol: "common.protocol",
        protocol_setting: "error.api.validate_key.protocol_setting",
        network: "common.network_type",
        network_setting: "error.api.validate_key.network_setting",
        security: "server.edit.transfer_encrypt",
        security_setting: "error.api.validate_key.security_setting",
        mux: "server.edit.mux"
    },
    routing: {
        proxy: "routing.edit.proxy_address",
        direct: "routing.edit.direct_address",
        block: "routing.edit.block_address",
        port: "routing.edit.target_port",
        network: "routing.edit.target_network",
        protocol: "routing.edit.target_protocol",
        servers: "routing.edit.target_server"
    },
    subscribe: {
        name: "common.name",
        url: "subscribe.edit.subscribe_url",
        mux: "server.edit.mux"
    }
};


export default function (target, data) {
    let error_text;
    for (const error in ErrorMessage) {
        if (toInteger(error) === data.code) {
            const text = get(ErrorMessage, `${error}.text`);
            if (isFunction(text)) {
                error_text = text(target, data.data)
            } else {
                let name = has(ErrorName, target) ? i18n.t(get(ErrorName, target)) : "";
                error_text = i18n.t(`error.api.${error}`, {value: name});
            }
        }
    }
    if (!isUndefined(error_text)) {
        Message.error({
            message: error_text
        });
    }
    if (has(data, "errors")) {
        Message.error({
            message: data.message
        });
    }
    return Promise.reject(data);
}
