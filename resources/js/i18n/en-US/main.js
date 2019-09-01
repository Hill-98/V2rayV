import el from "element-ui/lib/locale/lang/en";
import tips from "./tips";

export default {
    ...el,
    common: {
        action: "Action",
        address: "Address",
        all: "All",
        ask_delete: "Are you sure you want to delete?",
        ask_to_quit: "You have not saved yet, are you sure you want to quit?",
        cancel: "Cancel",
        client: "Client",
        can_not_empty: "{value} cannot be empty",
        click_me: "Click me",
        confirm: "Confirm",
        delete: "Delete",
        disable: "Disable",
        fail: "Fail",
        edit: "Edit",
        enable: "Enable",
        example_config: "Example config",
        domain_name: "Domain name",
        generate: "Generate",
        group: "Group",
        i_know: "I know",
        import: "Import",
        language: "Language",
        loading: "Loading...",
        method: "Method",
        more: "More",
        name: "Name",
        network: "Network",
        network_type: "Network type",
        no: "No",
        password: "Password",
        path: "Path",
        please_select: "Please select",
        please_select_as: "Please select {value}",
        port: "Port",
        prompt: "Prompt",
        protocol: "Protocol",
        proxy: "Proxy",
        refresh: "Refresh",
        save: "Save",
        save_success: "Save success",
        select_least_one: "Select at least one {value}",
        server: "Server",
        share: "Share",
        success: "Success",
        unknown: "Unknown",
        update: "Update",
        username: "Username",
        value_split: "Multiple values ​​with , split",
        version: "Version",
        wrong_input: "Please enter the correct {value}",
        yes: "Yes",
    },
    about: {
        not_update: "V2ray and V2rayV are the latest versions",
        v2ray_update: "A new version of V2ray has been detected ({version}), which is being updated in the background.",
        vvv_update: "A new version ({version}) of V2rayV has been detected. Do you want to download it from the download page?"
    },
    dns: {
        title: "You can manage V2rayV's DNS configuration here.",
        view_example: "View sample config"
    },
    error: {
        network: {
            common: "Oops, something went wrong, {error}.",
            response: "no resources were received",
            request: "request failed"
        },
        api: {
            103: "Local port is already occupied by another server",
            401: "Duplicate Subscription Name",
            402: "Duplicate Subscription URL",
            901: {
                empty: "{value} cannot be empty",
                invalid:"{value} validation error",
                unknown: "Unknown validation error"
            },
            902: "{value} not exist",
            903: "{value} already exists",
            904: "{value} save fail",
            905: "{value} delete fail",
            911: "Check for update fail",
            name: {
                server: "Server",
                routing: "Routing rule",
                subscribe: "Subscribe",
                setting: "Setting"
            },
            validate_key: {
                protocol_setting: "Protocol settings",
                network_setting: "Network settings",
                security_setting: "Transport encrypt settings"
            }
        }
    },
    menu: {
        about: "About",
        servers: "Server Manage",
        routing: "Routing Rule",
        routing_default: "Default Rule",
        routing_custom: "Custom Rule",
        dns: "DNS Manage",
        subscribe: "Subscribe List",
        setting: "Setting"
    },
    routing: {
        target_network: "Target network",
        target_port: "Target port",
        target_protocol: "Target protocol",
        default: {
            title: "The default routing rule applies only to the main server"
        },
        edit: {
            block_address: "Block address",
            custom_address: "Custom address",
            custom_placeholder: "Custom address, Multiple values ​​with , split.",
            direct_address: "Direct address",
            proxy_address: "Proxy address",
            save_success: "Save Success !",
            target_server: "Target servers",
            ext_rules: {
                category_ads_all: "Ad domain name",
                cn_domain: "China domain name",
                cn_ip: "China IP",
                facebook: "Facebook",
                google: "Google",
                non_cn_domain: "Common non-China domain name",
                private_ip: "Local and LAN IP",
                speedtest: "SpeedTest"
            }
        },
        custom: {
            add_rule: "Add routing rule",
            rule_count: "Routing rule count：{count}",
            tip: "You can set custom routing rules for one or more servers"
        }
    },
    server: {
        local_port: "Local port",
        edit: {
            allow_insecure: "Allow insecure connections",
            alter_id: "Alter ID",
            address: "Server address",
            cert_domain: "Certificate domain",
            congestion: "Congestion control",
            downlink_capacity: "Downlink bandwidth",
            encrypt_method: "Encrypt method",
            header_type: "Obfuscation type",
            http_header: "HTTP Header",
            http_method: "HTTP Method",
            http_version: "HTTP Version",
            http_header_example_config: "HTTP Header example config",
            key: "Key",
            json_object_format: "JSON Object format",
            mtu: "Maximum Transmission Unit",
            mux: "Multiplexing",
            mux_concurrency: "Max number of multiplexed connections",
            port: "Server port",
            read_buffer_size: "Read buffer size",
            transfer_encrypt: "Transport encrypt",
            tti: "Transmission Time Interval",
            uplink_capacity: "Uplink bandwidth",
            user_id: "User ID",
            write_buffer_size: "Write buffer size",
            udp_header_type: {
                bt: "Bittorrent traffic",
                dtls: "DTLS 1.2",
                not_guise: "No obfuscation",
                video_chat: "Video call data （Example：FaceTime）",
                wechat: "WeChat video call",
                wireguard: "WireGuard"
            }
        },
        index: {
            add_server: "Add server",
            address_dblclick: "Double click toggle Show/Hide",
            ask_encrypt: "Do you encrypt the share link?",
            export_as: "Export as {value} config",
            import_fail: "Import fail",
            import_fail_password: ", May be the password is incorrect.",
            import_success: "Import success, new: {new}.",
            import_partial_success: "Partially import success, new: {new}, fail: {fail}.",
            import_url: "Import from URL",
            main_server: "Main Server",
            paste_url: "Paste your share link here, one per line.",
            save_local: "Save to local",
            server_count: "Server count: {count}",
            set_main_server: "Set as main server",
            share_url: "Share URL",
            testing: "Testing...",
            test_delay: "Test Delay",
            test_fail: "Test fail",
            test_result: "Test result",
        }
    },
    setting: {
        main_port: "Main Port",
        main_http_port: "Main HTTP Port",
        allow_lan: "Allow LAN connect",
        log_level: "V2ray Log Level",
        auto_update_v2ray: "Auto update V2ray Core",
        update_v2ray_proxy: "Update V2ray Core with proxy",
        auto_start: "Boot Automatically",
        close_http_proxy: "* Set to 0 to turn off the HTTP proxy"
    },
    subscribe: {
        edit: {
            auto_update: "Auto update",
            proxy_update: "Update via proxy",
            subscribe_url: "Subscribe URL"
        },
        index: {
            add_subscribe: "Add subscribe",
            encrypt: "Encrypt",
            last_update_status: "Last update status",
            last_update_time: "Last update time",
            not_update: "Not update",
            server_count: "Server count",
            subscribe_count: "Subscribe count：{count}",
            subscribe_update: "Update request submitted"
        }
    },
    tips,
    title: {
        about: "About",
        servers: "Server Manage",
        servers_add: "Add Server",
        servers_edit: "Edit Server",
        routing_default: "Default Routing Rule",
        routing_custom: "Custom Routing Rule",
        routing_add: "Add Routing Rule",
        routing_edit: "Edit Routing Rule",
        dns: "DNS Manage",
        subscribe: "Subscribe List",
        subscribe_add: "Add Subscribe",
        subscribe_edit: "Edit Subscribe",
        setting: "Setting"
    }
}
