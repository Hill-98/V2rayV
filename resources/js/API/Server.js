import Common from "./Common";
import join from "lodash/join";
import {isArray, isEmpty, toInteger} from "lodash/lang";
import split from "lodash/split";

class Server extends Common {
    static prefix = "server";
    static all() {
        return this.client("get", `${this.prefix}-all`);
    }
    static switchEnable(id, enable) {
        return this.client("post", `${this.prefix}/switch?enable=${toInteger(enable)}`, id);
    }
    static exportShareUrl(id, encrypt) {
        if (!isArray(id)) id = [id];
        return this.client("get", `${this.prefix}/share-url/export?encrypt=${toInteger(encrypt)}&servers=${join(id, ",")}`);
    }
    static importShareUrl(urls, password) {
        urls = split(urls, "\n");
        if (!isEmpty(password)) password = `?password=` + password;
        return this.client("post", `${this.prefix}/share-url/import${password}`, urls);
    }
    static ping(id) {
        return this.client("get", `${this.prefix}/ping?id=${toInteger(id)}`);
    }
    static exportConfig(id, type) {
        if (!isArray(id)) id = [id];
        return this.client("get", `${this.prefix}/export-config?type=${type}&servers=${join(id, ",")}`);
    }
}

export default Server
