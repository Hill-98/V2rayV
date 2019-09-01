<?php
declare(strict_types=1);

namespace App\V2rayV\Validation;

use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class Server extends Validation
{
    protected $configKeys = [
        "address" => "string",
        "port" => "int",
        "protocol" => "string",
        "protocol_setting" => "array",
        "network" => "string",
        "network_setting" => "array",
        "security" => "string",
        "security_setting" => "array",
        "mux" => "array"
    ];

    /**
     * 地址是否正确
     *
     * @param string $value
     * @return bool
     */
    protected function isAddress(string $value): bool
    {
        /**
         * 判断是否是主机名或者 IP
         */
        return (filter_var($value, FILTER_VALIDATE_DOMAIN) === $value ||
            filter_var($value, FILTER_VALIDATE_IP) === $value);
    }

    /**
     * 协议是否正确
     *
     * @param string $value
     * @return bool
     */
    protected function isProtocol(string $value): bool
    {
        $list = [
            "shadowsocks",
            "socks",
            "vmess"
        ];
        return in_array($value, $list);
    }


    /**
     * 端口是否正确
     *
     * @param int $value
     * @return bool
     */
    protected function isPort(int $value): bool
    {
        return $this->validPort($value);
    }

    /**
     * 网络类型是否正确
     *
     * @param string $value
     * @return bool
     */
    protected function isNetwork(string $value): bool
    {
        $list = [
            "tcp",
            "kcp",
            "ws",
            "http",
            "quic"
        ];
        return in_array($value, $list);
    }

    /**
     * 安全配置库是否正确
     * @param string $value
     * @return bool
     */
    protected function isSecurity(string $value): bool
    {
        $list = [
            "none",
            "tls"
        ];
        return in_array($value, $list);
    }

    /**
     * Shadowsocks 协议是否正确
     *
     * @param array $config
     * @return bool
     */
    protected function isShadowsocksProtocol(array $config): bool
    {
        $encryptList = [
            "aes-256-cfb",
            "aes-128-cfb",
            "chacha20",
            "chacha20-ietf",
            "aes-256-gcm",
            "aes-128-gcm",
            "chacha20-poly1305",
            "chacha20-ietf-poly1305"
        ];
        if (empty($config["method"]) || empty($config["password"] || !isset($config["ota"]))) {
            return false;
        }
        // 验证加密方法是否正确以及密码是否是字符串
        return (in_array($config["method"], $encryptList) && is_string($config["password"]) && is_bool($config["ota"]));
    }

    /**
     * Socks 协议是否正确
     *
     * @param array $config
     * @return bool
     */
    protected function isSocksProtocol(array $config): bool
    {
        // 验证用户名和密码是否是字符串
        return ((!isset($config["user"]) && !isset($config["pass"])) ||
            (is_string($config["user"]) && is_string($config["pass"])));
    }

    /**
     * VMess 协议是否正确
     *
     * @param array $config
     * @return bool
     */
    protected function isVmessProtocol(array $config): bool
    {
        $securityList = [
            "aes-128-gcm",
            "chacha20-poly1305",
            "auto",
            "none"
        ];
        if (empty($config["id"]) || !isset($config["alterId"]) || empty($config["security"])) {
            return false;
        }
        // 验证加密方式是否正确、额外 ID 值是否合法以及 ID 是否为合法的 UUID
        return (in_array($config["security"], $securityList) && is_int($config["alterId"]) &&
            $config["alterId"] >= 0 && $config["alterId"] <= 65535 &&
            Uuid::isValid($config["id"]));
    }

    /**
     * TCP 网络配置是否正确
     *
     * @param array $config
     * @return bool
     */
    protected function isTcpNetwork(array $config): bool
    {
        $httpVersions = [
            "1.0",
            "1.1",
            "2"
        ];
        $httpMethods = [
            "GET",
            "POST",
            "HEAD",
            "PUT",
            "DELETE",
            "CONNECT",
            "OPTIONS",
            "TRACE",
            "PATCH"
        ];
        if (empty($config["header"]["type"])) {
            return false;
        }
        if ($config["header"]["type"] === "none") {
            return true;
        }
        return ($config["header"]["type"] === "http" && !empty($config["header"]["request"]) &&
            in_array($config["header"]["request"]["version"], $httpVersions) &&
            in_array($config["header"]["request"]["method"], $httpMethods) &&
            is_array($config["header"]["request"]["path"]));
    }

    /**
     * KCP 网络配置是否正确
     *
     * @param array $config
     * @return bool
     */
    protected function isKcpNetwork(array $config): bool
    {
        $configKeys = [
            "mtu" => "int",
            "tti" => "int",
            "uplinkCapacity" => "int",
            "downlinkCapacity" => "int",
            "congestion" => "bool",
            "readBufferSize" => "int",
            "writeBufferSize" => "int",
            "header" => "array"
        ];
        $headerTypes = [
            "none",
            "srtp",
            "utp",
            "wechat-video",
            "dtls",
            "wireguard"
        ];
        foreach ($configKeys as $key => $value) {
            if (!isset($config[$key]) || !call_user_func("is_$value", $config[$key])) {
                return false;
            }
        }
        return ($config["mtu"] >= 596 && $config["mtu"] <= 1460 &&
            $config["tti"] >= 10 && $config["tti"] <= 100 &&
            $config["uplinkCapacity"] >= 0 && $config["downlinkCapacity"] >= 0 &&
            $config["readBufferSize"] >= 0 && $config["writeBufferSize"] >= 0 &&
            !empty($config["header"]["type"]) && in_array($config["header"]["type"], $headerTypes));
    }

    /**
     * WebSocket 网络配置是否正确
     *
     * @param array $config
     * @return bool
     */
    protected function isWsNetwork(array $config): bool
    {
        return (!empty($config["path"]) && Str::startsWith($config["path"], "/"));
    }

    /**
     * HTTP/2 网络配置是否正确
     *
     * @param array $config
     * @return bool
     */
    protected function isHttpNetwork(array $config): bool
    {
        if (empty($config["host"]) || empty($config["path"]) ||
            !Str::startsWith($config["path"], "/")) {
            return false;
        }
        foreach ($config["host"] as $value) {
            if (filter_var($value, FILTER_VALIDATE_DOMAIN) !== $value) {
                return false;
            }
        }
        return true;
    }

    /**
     * QUIC 网络配置是否正确
     *
     * @param array $config
     * @return bool
     */
    protected function isQuicNetwork(array $config): bool
    {
        $securityList = [
            "none",
            "aes-128-gcm",
            "chacha20-poly1305"
        ];
        $headerTypes = [
            "none",
            "srtp",
            "utp",
            "wechat-video",
            "dtls",
            "wireguard"
        ];
        if (empty($config["security"]) || empty($config["header"]["type"]) ||
            !in_array($config["security"], $securityList) || !in_array($config["header"]["type"], $headerTypes)) {
            return false;
        }
        if ($config["security"] !== "none") {
            if (empty($config["key"]) || !is_string($config["key"])) {
                return false;
            }
        }
        return true;
    }


    /**
     * NONE 安全配置是否正确
     *
     * @return bool
     */
    protected function isNoneSecurity(): bool
    {
        return true;
    }

    /**
     * TLS 安全配置是否正确
     *
     * @param array $config
     * @return bool
     */
    protected function isTlsSecurity(array $config): bool
    {
        $alpnList = [
            "http/1.1",
            "h2"
        ];
        $host = $config["serverName"] ?? "";
        if (!empty($config["serverName"]) && filter_var($host, FILTER_VALIDATE_DOMAIN) !== $host) {
            return false;
        }
        if (isset($config["allowInsecure"]) && !is_bool($config["allowInsecure"])) {
            return false;
        }
        if (isset($config["alpn"])) {
            if (empty($config["alpn"]) || !is_array($config["alpn"])) {
                return false;
            }
            foreach ($config["alpn"] as $value) {
                if (!in_array($value, $alpnList)) {
                    return false;
                }
            }
        }
        return true;
    }
}
