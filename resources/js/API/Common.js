import client from "../client";
import {isInteger, toInteger} from "lodash/lang";

class common {
    static client = client;
    static prefix = "";
    static index(page) {
        if (!isInteger(page) || page === 0) page = 1;
        return this.client("get", `${this.prefix}/?page=${page}`);
    }
    static get(id) {
        return this.client("get", `${this.prefix}/${toInteger(id)}`);
    }
    static save(data, id) {
        id = toInteger(id);
        if (id === 0) {
            id = "";
        }
        return this.client(id === "" ? "post" : "put", `${this.prefix}/${id}`, data);
    }
    static delete(id) {
        return this.client("delete", `${this.prefix}/${toInteger(id)}`);
    }
}

export default common;
