import toInteger from "lodash/toInteger";

export default (data) => {
    data.main_port = toInteger(data.main_port);
    data.main_http_port = toInteger(data.main_http_port)
    return data;
}
