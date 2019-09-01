import Common from "./Common";
import toInteger from "lodash/toInteger";

class Routing extends Common {
    static prefix = "routing";

    static switchEnable(id, enable) {
        return this.client("post", `${this.prefix}/switch?enable=${toInteger(enable)}`, id);
    }

    static save(data, id) {
        if (id === "default") {
            id = "0";
        } else {
            id = toInteger(id);
            if (id === 0) {
                id = "";
            }
        }
        return this.client(id === "" ? "post" : "put", `${this.prefix}/${id}`, data);
    }
}

export default Routing
