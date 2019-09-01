import has from "lodash/has";
import toInteger from "lodash/toInteger";

export default (data) => {
    if (has(data.mux, "concurrency")) data.mux.concurrency = toInteger(data.mux.concurrency);
    return data;
}
