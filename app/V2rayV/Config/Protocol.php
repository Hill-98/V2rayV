<?php

namespace App\V2rayV\Config;

use App\Models\Server as ServerModel;

class Protocol
{
    /**
     * 生成 Shadowsocks 协议出站配置
     *
     * @param ServerModel $config
     * @return array
     */
    public function shadowsocksOutbound(ServerModel $config): array
    {
        /** @var array $protocolSetting */
        $protocolSetting = $config->protocol_setting;
        $setting = [
            "servers" => [
                [
                    "address" => $config->address,
                    "port" => $config->port,
                    "method" => $protocolSetting["method"],
                    "password" => $protocolSetting["password"],
                    "ota" => $protocolSetting["ota"],
                ]
            ]
        ];
        return $setting;
    }

    /**
     * 生成 Socks 连接出站配置
     *
     * @param ServerModel $config
     * @return array
     */
    public function socksOutbound(ServerModel $config): array
    {
        /** @var array $protocolSetting */
        $protocolPetting = $config->protocol_setting;
        $setting = [
            "servers" => [
                [
                    "address" => $config->address,
                    "port" => $config->port,
                ]
            ]
        ];
        if (!empty($protocolPetting["user"]) && !empty($protocolPetting["pass"])) {
            $setting["users"] = [$protocolPetting];
        }
        return $setting;
    }

    /**
     * 生成 Vmess 连接出站配置
     *
     * @param ServerModel $config
     * @return array
     */
    public function vmessOutbound(ServerModel $config): array
    {
        /** @var array $protocolSetting */
        $protocolSetting = $config->protocol_setting;
        $setting = [
            "vnext" => [
                [
                    "address" => $config->address,
                    "port" => $config->port,
                    "users" => [
                        $protocolSetting,
                    ]
                ]
            ]
        ];
        return $setting;
    }

    /**
     * 生成 Shadowsocks 协议入站配置
     *
     * @param ServerModel $config
     * @return array
     */
    public function shadowsocksInbound(ServerModel $config): array
    {
        /** @var array $protocolSetting */
        $protocolSetting = $config->protocol_setting;
        $setting = [
            "method" => $protocolSetting["method"],
            "password" => $protocolSetting["password"],
            "ota" => $protocolSetting["ota"],
            "network" => "tcp,udp"
        ];
        return $setting;
    }

    /**
     * 生成 Socks 连接入站配置
     *
     * @param ServerModel $config
     * @return array
     */
    public function socksInbound(ServerModel $config): array
    {
        /** @var array $protocolSetting */
        $protocolSetting = $config->protocol_setting;
        $setting = [
            "auth" => "noauth",
            "udp" => true,
        ];
        if (!empty($protocolSetting["user"]) && !empty($protocolSetting["pass"])) {
            $setting["auth"] = "password";
            $setting["accounts"] = [$protocolSetting];
        }
        return $setting;
    }

    /**
     * 生成 Vmess 连接入站配置
     *
     * @param ServerModel $config
     * @return array
     */
    public function vmessInbound(ServerModel $config): array
    {
        /** @var array $protocolSetting */
        $protocolSetting = $config->protocol_setting;
        $setting = [
            "clients" => [$protocolSetting],
        ];
        return $setting;
    }
}
