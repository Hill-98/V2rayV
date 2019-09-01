import client from "../client";

class Dns {
    static prefix = "dns";

    static get() {
        return client("get", this.prefix);
    }

    static put(data) {
        return client("put", this.prefix, {data: data});
    }
}

export default Dns;
