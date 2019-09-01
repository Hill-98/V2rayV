import axios from "axios";
import has from "lodash/has";
import split from "lodash/split";
import ApiError from "./error/api";
import NetworkError from "./error/network";

const client = axios.create({
    baseURL: "/api",
    headers: {"X-Auth-Token": "123456"}
});
client.interceptors.request.use(request => request, error => NetworkError(error));
client.interceptors.response.use(response => {
    if (response.data.success) {
        return Promise.resolve(response);
    } else {
        const uri = split(response.config.url, "/")[2];
        let target = uri.match(/(.*)\?/);
        if (target) target = target[1]; else target = uri;
        return ApiError(target, response.data);
    }
}, error => NetworkError(error));

export default (method, url, data) => {
    if (!has(client, method)) {
        return Promise.reject();
    }
    return new Promise((resolve, reject) => {
        client({
            method: method,
            url: url,
            data: data
        })
            .then(response => resolve(response.data))
            .catch(error => reject(error));
    });
};
