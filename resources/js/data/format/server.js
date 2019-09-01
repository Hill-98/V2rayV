import {isArray, isNumber, isObject, toInteger} from "lodash/lang";
import {has} from "lodash/object";
import join from "lodash/join";
import map from "lodash/map";
import {split, trim} from "lodash/string";

export default (data) => {
    data.port = toInteger(data.port);
    if (data.protocol === "vmess") {
        if (has(data.protocol_setting, "alterId")) {
            data.protocol_setting.alterId = toInteger(data.protocol_setting.alterId);
        } else {
            data.protocol_setting.alterId = 0;
        }
    }
    if (data.network === "kcp") {
        for (const i in data.network_setting) {
            if (isNumber(data.network_setting[i])) data.network_setting[i] = toInteger(data.network_setting[i]);
        }
    }
    if (data.network === "tcp" && has(data.network_setting, "header.type") && data.network_setting.header.type === "http") {
        if (has(data.network_setting, "header.request.path")) {
            if (isArray(data.network_setting.header.request.path)) {
                data.network_setting.header.request.path = join(data.network_setting.header.request.path, ", ");
            } else if (isArray(data.network_setting.header.request.path)) {
                data.network_setting.header.request.path = map(split(data.network_setting.header.request.path, ","), trim);
            }
        }
        if (has(data.network_setting, "header.request.headers")) {
            if (isObject(data.network_setting.header.request.headers)) {
                data.network_setting.header.request.headers = JSON.stringify(data.network_setting.header.request.headers, null, 4);
            } else {
                data.network_setting.header.request.headers = JSON.parse(data.network_setting.header.request.headers);

            }
        }
    }
    if (data.network === "ws" && has(data.network_setting, "headers")) {
        if (isObject(data.network_setting.headers)) {
            data.network_setting.headers = JSON.stringify(data.network_setting.headers);
        } else {
            data.network_setting.headers = JSON.parse(data.network_setting.headers);
        }
    }
    if (data.network === "http" && has(data.network_setting, "host")) {
        if (isArray(data.network_setting.host)) {
            data.network_setting.host = join(data.network_setting.host, ", ");
        } else if (isArray(data.network_setting.host)) {
            data.network_setting.host = map(split(data.network_setting.host, ","), trim);
        }
    }
    if (isArray(data.security_setting)) {
        data.security_setting = {
            alpn: []
        };
    }
    if (has(data.mux, "concurrency")) data.mux.concurrency = toInteger(data.mux.concurrency);
    if (has(data, "local_port")) data.local_port = toInteger(data.local_port);
    console.log(data);
    return data;
}
