import about from "./tips/about";
import dns_view_example from "./tips/dns.view_example";
import routing_custom_address from "./tips/routing.custom_address";
import server_local_port from "./tips/server.local_port";
import server_mux from "./tips/server.mux";
import server_share_need from "./tips/server.share_need";

export default {
    about,
    dns: {
        view_example: dns_view_example
    },
    routing: {
        custom_address: routing_custom_address,
    },
    server: {
        local_port: server_local_port,
        mux: server_mux,
        share_need: server_share_need,
    }
}
