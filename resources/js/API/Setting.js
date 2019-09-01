import client from "../client";
import toInteger from "lodash/toInteger";

class Setting {
    static prefix = "setting";

    static get() {
        return client("get", this.prefix);
    }

    static save(data) {
        return client("put", this.prefix, data);
    }

    static getMainServer() {
        return client("get", `${this.prefix}/main-server`)
    }

    static setMainServer(id) {
        return client("put", `${this.prefix}/main-server?id=${toInteger(id)}`)
    }
}

export default Setting;
