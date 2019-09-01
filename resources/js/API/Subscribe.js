import Common from "./Common";
import toInteger from "lodash/toInteger";

class Subscribe extends Common {
    static prefix = "subscribe";

    static subscribe_update(id) {
        return this.client("post", `${this.prefix}/update/`, {id: toInteger(id)});
    }
}

export default Subscribe
