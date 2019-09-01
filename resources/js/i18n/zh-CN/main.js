import el from "element-ui/lib/locale/lang/zh-CN";
import tips from "./tips";

export default {
    ...el,
    common: {
        action: "操作",
        address: "地址",
        all: "全部",
        ask_delete: "你确定要删除吗？",
        ask_to_quit: "你还没有保存，确定要退出吗？",
        cancel: "取消",
        client: "客户端",
        can_not_empty: "{value} 不能为空",
        click_me: "点我",
        confirm: "确定",
        delete: "删除",
        disable: "禁用",
        fail: "失败",
        edit: "编辑",
        enable: "启用",
        example_config: "示例配置",
        domain_name: "域名",
        generate: "生成",
        group: "群组",
        i_know: "我知道了",
        import: "导入",
        language: "语言",
        loading: "拼命加载中...",
        method: "方法",
        more: "更多",
        name: "名称",
        network: "网络",
        network_type: "网络类型",
        no: "否",
        password: "密码",
        path: "路径",
        please_select: "请选择",
        please_select_as: "请选择 {value}",
        port: "端口",
        prompt: "提示",
        protocol: "协议",
        proxy: "代理",
        refresh: "刷新",
        save: "保存",
        save_success: "保存成功",
        select_least_one: "至少选择一个 {value}",
        server: "服务器",
        share: "分享",
        success: "成功",
        unknown: "未知",
        update: "更新",
        username: "用户名",
        value_split: "多个值以 , 分割",
        version: "版本",
        wrong_input: "请输入正确的 {value}",
        yes: "是",
    },
    about: {
        not_update: "V2ray 和 V2rayV 是最新版本",
        v2ray_update: "检测到 V2ray 有新版本 ({version})，正在后台更新中。",
        vvv_update: "检测到 V2rayV 有新版本 ({version})，是否前往下载页下载？"
    },
    dns: {
        title: "你可以在这里管理 V2rayV 的 DNS 配置",
        view_example: "查看示例配置",
    },
    error: {
        network: {
            common: "糟糕，出错了，{error}。",
            response: "未收到资源",
            request: "请求发送失败"
        },
        api: {
            103: "本地端口已被其他服务器占用",
            401: "订阅名称重复",
            402: "订阅 URL 重复",
            901: {
                empty: "{value}不能为空",
                invalid:"{value}校验错误",
                unknown: "未知的数据校验错误"
            },
            902: "{value}不存在",
            903: "{value}已存在",
            904: "{value}保存失败",
            905: "{value}删除失败",
            911: "检查更新失败",
            name: {
                server: "服务器",
                routing: "路由规则",
                subscribe: "订阅",
                setting: "设置"
            },
            validate_key: {
                protocol_setting: "协议设置",
                network_setting: "网络设置",
                security_setting: "传输层加密设置"
            }
        }
    },
    menu: {
        about: "关于",
        servers: "服务器管理",
        routing: "路由规则",
        routing_default: "默认规则",
        routing_custom: "自定义规则",
        dns: "DNS 管理",
        subscribe: "订阅列表",
        setting: "设置"
    },
    routing: {
        target_network: "目标网络",
        target_port: "目标端口",
        target_protocol: "目标协议",
        default: {
            title: "默认路由规则只应用于主服务器",
        },
        edit: {
            block_address: "阻止地址",
            custom_address: "自定义地址",
            custom_placeholder: "自定义地址，多个值以 , 分割。",
            direct_address: "直连地址",
            proxy_address: "代理地址",
            save_success: "保存成功！",
            target_server: "目标服务器",
            ext_rules: {
                category_ads_all: "广告域名",
                cn_domain: "中国域名",
                cn_ip: "中国 IP",
                facebook: "Facebook",
                google: "Google",
                non_cn_domain: "常见的非中国域名",
                private_ip: "本地及局域网 IP",
                speedtest: "SpeedTest"
            }
        },
        custom: {
            add_rule: "添加路由规则",
            rule_count: "路由规则数量：{count}",
            tip: "你可以针对一个或多个服务器设置自定义路由规则"
        }
    },
    server: {
        local_port: "本地端口",
        edit: {
            allow_insecure: "允许不安全连接",
            alter_id: "额外 ID",
            address: "服务器地址",
            cert_domain: "证书域名",
            congestion: "拥塞控制",
            downlink_capacity: "下行链路容量",
            encrypt_method: "加密方法",
            header_type: "伪装类型",
            http_header: "HTTP 头",
            http_method: "HTTP 方法",
            http_version: "HTTP 版本",
            http_header_example_config: "HTTP 头示例配置",
            key: "密钥",
            json_object_format: "JSON 对象格式",
            mtu: "最大传输单元",
            mux: "多路复用",
            mux_concurrency: "最大并发连接数",
            port: "服务器端口",
            read_buffer_size: "读取缓冲区大小",
            transfer_encrypt: "传输层加密",
            tti: "传输时间间隔",
            uplink_capacity: "上行链路容量",
            user_id: "用户 ID",
            write_buffer_size: "写入缓冲区大小",
            udp_header_type: {
                bt: "BT 下载数据",
                dtls: "DTLS 1.2",
                not_guise: "不进行伪装",
                video_chat: "视频通话数据 （如：FaceTime）",
                wechat: "微信视频通话",
                wireguard: "WireGuard"
            }
        },
        index: {
            add_server: "添加服务器",
            address_dblclick: "双击切换显示/隐藏",
            ask_encrypt: "是否加密分享链接",
            export_as: "导出为{value}配置",
            import_fail: "导入失败",
            import_fail_password: "，可能是密码不正确。",
            import_success: "导入成功，新增：{new}。",
            import_partial_success: "部分导入成功，新增：{new}，失败：{fail}。",
            import_url: "从 URL 导入",
            main_server: "主服务器",
            paste_url: "在这里粘贴你的分享链接，每行一个。",
            save_local: "保存到本地",
            server_count: "服务器数量：{count}",
            set_main_server: "设置为主服务器",
            share_url: "分享链接",
            testing: "努力测试中...",
            test_delay: "测试延迟",
            test_fail: "测试失败",
            test_result: "测试结果",
        }
    },
    setting: {
        main_port: "主服务器端口",
        main_http_port: "主服务器 HTTP 端口",
        allow_lan: "允许局域网连接",
        log_level: "V2ray 日志等级",
        auto_update_v2ray: "自动更新 V2ray Core",
        update_v2ray_proxy: "通过代理更新 V2ray Core",
        auto_start: "开机自动启动",
        close_http_proxy: "* 设置为 0 以关闭 HTTP 代理"
    },
    subscribe: {
        edit: {
            auto_update: "自动更新",
            proxy_update: "通过代理更新",
            subscribe_url: "订阅链接"
        },
        index: {
            add_subscribe: "添加订阅",
            encrypt: "加密",
            last_update_status: "最后更新状态",
            last_update_time: "最后更新时间",
            not_update: "没有更新",
            server_count: "服务器数量",
            subscribe_count: "订阅数量：{count}",
            subscribe_update: "更新请求已提交"
        }
    },
    tips,
    title: {
        about: "关于",
        servers: "服务器管理",
        servers_add: "添加服务器",
        servers_edit: "编辑服务器",
        routing_default: "默认路由规则",
        routing_custom: "自定义路由规则",
        routing_add: "添加路由规则",
        routing_edit: "编辑路由规则",
        dns: "DNS 管理",
        subscribe: "订阅列表",
        subscribe_add: "添加订阅",
        subscribe_edit: "编辑订阅",
        setting: "设置"
    }
}
