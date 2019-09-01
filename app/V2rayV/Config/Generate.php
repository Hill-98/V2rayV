<?php
declare(strict_types=1);

namespace App\V2rayV\Config;

use App\Exceptions\V2ray\NotExist;
use App\V2rayV\DefaultRouting;
use App\V2rayV\File\AccessLog;
use App\V2rayV\File\Dns;
use App\V2rayV\File\ErrorLog;
use App\V2rayV\Routing;
use App\V2rayV\Server;
use App\V2rayV\Setting;
use Illuminate\Support\Str;

class Generate
{
    /**
     * V2rayV 配置
     *
     * @var array $config
     */
    private $config;

    /**
     * 主服务器 ID
     *
     * @var int $mainServer
     */
    private $mainServer;
    /**
     * 启用的服务器
     *
     * @var \Illuminate\Support\Collection $serverList
     */
    private $serverList;
    /**
     * 启用的路由规则
     *
     * @var \Illuminate\Support\Collection $routingList
     */
    private $routingList;
    /**
     * 有自定义规则的服务器
     *
     * @var array $customRuleServers
     */
    private $customRuleServers;


    private $serverModel;
    private $routingModel;
    private $defaultRouting;
    private $setting;
    private $base;

    /**
     * Generate constructor.
     * @param Server $server
     * @param Routing $routing
     * @param DefaultRouting $defaultRouting
     * @param Setting $setting
     */
    public function __construct(Server $server, Routing $routing, DefaultRouting $defaultRouting, Setting $setting)
    {
        $this->serverModel = $server;
        $this->routingModel = $routing;
        $this->defaultRouting = $defaultRouting;
        $this->customRuleServers = [];
        $this->setting = $setting;
        $this->base = new Base();
    }

    /**
     * @return mixed
     */
    public function __invoke(): array
    {
        if ($this->serverModel->list(false)->count() === 0) {
            return [];
        }
        // 导入默认配置
        $this->config = include(__DIR__ . "/config.sample.php");
        $this->mainServer = $this->setting->getMainServer();
        $this->serverList = $this->serverModel->list(false, ["enable"]);
        $this->routingList = $this->routingModel->list(false, ["enable"]);
        // 如果主服务器不存在，选择第一个服务器当作主服务器。
        try {
            $server = $this->serverModel->get($this->mainServer);
        } catch (NotExist $e) {
            $server = $this->serverModel->list(false)->first();
            $this->mainServer = $server->id;
        }
        if ($this->serverList->where("id", $server->id)->isEmpty()) {
            $this->serverList->push($server);
        }
        if ($this->serverList->count() === 0) {
            return [];
        }
        $this->config["log"] = $this->logConfig();
        $this->config["dns"] = $this->dnsConfig();
        if (empty($this->config["dns"])) {
            unset($this->config["dns"]);
        }
        $this->config["inbounds"] = array_merge(
            $this->config["inbounds"],
            $this->base->generateInboundsClient(
                $this->serverList,
                false,
                $this->mainServer,
                $this->setting->main_port,
                $this->setting->main_http_port
            )
        );
        $this->config["outbounds"] = array_merge(
            $this->config["outbounds"],
            $this->base->generateOutbounds($this->serverList, $this->mainServer)
        );
        $this->config["routing"]["rules"] = array_merge($this->config["routing"]["rules"], $this->routingRules());
        if ($this->setting->allow_lan) {
            foreach ($this->config["inbounds"] as &$inbound) {
                $protocol = $inbound["protocol"];
                if ($protocol === "socks" || $protocol === "http") {
                    $inbound["listen"] = "0.0.0.0";
                }
            }
        }
        return $this->config;
    }

    /**
     * 生成日志配置
     *
     * @return array
     */
    private function logConfig(): array
    {
        $config = [
            "access" => (new AccessLog())->getPath(true),
            "error" => (new ErrorLog())->getPath(true),
            "loglevel" => $this->setting->log_level
        ];
        return $config;
    }

    /**
     * 生成 DNS 配置
     *
     */
    private function dnsConfig(): array
    {
        $config = [
            "hosts" => [],
            "servers" => [],
            "tag" => "dns"
        ];
        $dnsData = (new Dns())->readFile();
        if (empty($dnsData)) {
            return [];
        }
        $dnsItems = explode(",", $dnsData);
        foreach ($dnsItems as $item) {
            $item = trim($item);
            if (empty($item)) {
                continue;
            }
            $data = explode(" ", $item);
            $count = count($data);
            if ($count === 1) { // DNS 服务器
                if (!Str::startsWith($data[0], "$")) {
                    $config["servers"][] = $data[0];
                }
            } elseif (Str::startsWith($data[0], "$") && $count >= 2) { // 指定域名 DNS 服务器
                $dnsServer = explode("#", Str::after($data[0], "$"));
                $serverConfig = [
                    "address" => $dnsServer[0],
                    "port" => isset($dnsServer[1]) ? intval($dnsServer[1]) : 53,
                    "domains" => []
                ];
                unset($data[0]);
                foreach ($data as $value) {
                    $serverConfig["domains"][] = $value;
                }
                $config["servers"][] = $serverConfig;
            } elseif ($count === 2) { // Hosts
                if (filter_var($data[0], FILTER_VALIDATE_IP) === $data[0]) {
                    $config["hosts"][$data[1]] = $data[0];
                }
            }
        }
        if (empty($config["hosts"])) {
            $config["hosts"] = (object)[];
        }
        return $config;
    }

    /**
     * 生成路由规则
     *
     * @return array
     */
    private function routingRules(): array
    {
        $ruleConfig = [
            "type" => "field",
            "inboundTag" => [],
            "outboundTag" => ""
        ];
        $routingRules = [];
        /** @var \App\Models\Routing $routing */
        // 遍历已启用规则并生成
        foreach ($this->routingList as $routing) {
            $routingRules = array_merge($routingRules, $this->generateRules($routing->toArray()));
        }
        // 默认规则生成
        $defaultRule = $this->defaultRouting->get();
        $routingRules = array_merge($routingRules, $this->generateRules($defaultRule, true));
        $serverRules = [];
        // 遍历服务器并设置代理规则
        foreach ($this->serverList as $server) {
            $serverId = strval($server->id);
            $tag = "server-";
            if ($server->id === $this->mainServer) {
                $tag .= "main-";
            } else {
                $tag .= "${serverId}-";
            }
            $tagIn = "${tag}in";
            $tagOut = "${tag}out";
            $serverRule = $ruleConfig;
            // 当前服务器是否有自定义代理规则
            if (isset($this->customRuleServers[$serverId])) {
                $rule = $this->customRuleServers[$serverId];
                $serverRule["network"] = $rule["network"];
                if (!empty($rule["port"])) {
                    $serverRule["port"] = $rule["port"];
                }
                // 如果设置了流量类型，启用对应入站连接的流量侦测
                if (!empty($rule["protocol"])) {
                    $serverRule["protocol"] = $rule["protocol"];
                    foreach ($this->config["inbounds"] as &$item) {
                        if ($item["tag"] === $tagIn || $item["tag"] === $tagIn . "-http") {
                            $item["sniffing"]["enabled"] = true;
                        }
                    }
                };
            }
            $serverRule["inboundTag"][] = $tagIn;
            $serverRule["outboundTag"] = $tagOut;
            $serverRules[] = $serverRule;
        }
        $routingRules = array_merge($routingRules, $serverRules);
        return $routingRules;
    }

    /**
     * 生成路由规则辅助方法
     *
     * @param array $config
     * @param bool $default 是否是默认规则
     * @return array
     */
    private function generateRules(array $config, bool $default = false): array
    {
        $ruleConfig = [
            "type" => "field",
            "domain" => [],
            "ip" => [],
            "inboundTag" => [],
            "outboundTag" => ""
        ];
        $rules = [
            "proxy" => $ruleConfig,
            "direct" => $ruleConfig,
            "block" => $ruleConfig
        ];
        // 处理端口文本
        if (Str::endsWith($config["port"], ",")) {
            $config["port"] = substr($config["port"], 0, -1);
        }
        // 遍历代理、直连、阻止列表
        foreach ($rules as $key => &$item) {
            if (empty($config[$key])) {
                unset($rules[$key]);
            }
            foreach ($config[$key] as $value) {
                $ip = explode("/", $value)[0];
                // 是否属于 IP 列表的规则
                if (Str::startsWith($value, "iext:") || Str::startsWith($value, "geoip:") ||
                    filter_var($ip, FILTER_VALIDATE_IP) === $ip) {
                    if (Str::startsWith($value, "iext:")) {
                        $value = Str::after($value, "i");
                    }
                    $item["ip"][] = $value;
                } else {
                    $item["domain"][] = $value;
                }
            }
        }
        $servers = [];
        if ($default) {
            $servers[] = $this->mainServer;
        } else {
            // 遍历适用于此规则的服务器
            if ($config["servers"][0] === "all") {
                /** @var Server $server */
                foreach ($this->serverList as $server) {
                    if ($server->id !== $this->mainServer || !$default) {
                        $servers[] = $server->id;
                    }
                }
            } else {
                foreach ($config["servers"] as $server) {
                    // 服务器是否存在
                    if (($server !== $this->mainServer || !$default) &&
                        $this->serverList->where("id", $server)->isNotEmpty()) {
                        $servers[] = $server;
                    }
                }
            }
        }
        $serverInTags = [];
        $serverOutTags = [];
        // 遍历适用于规则的服务器列表，生成 TAG 和服务器代理规则配置
        foreach ($servers as $server) {
            $serverId = strval($server);
            // 是否已存在适用于当前服务器规则的配置
            if (isset($this->customRuleServers[$serverId])) {
                $serverRule = $this->customRuleServers[$serverId];
            } else {
                $serverRule = [];
            }
            /**
             * 网络类型、端口、协议 适用于整个服务器代理而不匹配 IP 和域名
             * 只有优先级更高的路由规则生效
             */
            if (!isset($serverRule["network"])) {
                $serverRule["network"] = $config["network"];
            }
            if (!empty($config["port"])) {
                if (empty($serverRule["port"])) {
                    $serverRule["port"] = $config["port"];
                } else {
                    $serverRule["port"] .= "," . $config["port"];
                }
            }
            if (empty($serverRule["protocol"]) && !empty($config["protocol"])) {
                $serverRule["protocol"] = $config["protocol"];
            }

            $this->customRuleServers[$serverId] = $serverRule;
            $tag = "server-";
            if ($server === $this->mainServer) {
                $tag .= "main-";
            } else {
                $tag .= "$server-";
            }
            $serverInTags[] = "${tag}in";
            $serverOutTags[] = "${tag}out";
        }
        // 规则导出
        $result = [];
        // 导出匹配 IP 或域名的规则
        if (isset($rules["block"])) {
            $rule = $rules["block"];
            $rule["inboundTag"] = $serverInTags;
            $rule["outboundTag"] = "block";
            $this->routingRuleFilter($rule, $result);
        }
        if (isset($rules["proxy"])) {
            for ($i = 0; $i < count($serverInTags); $i++) {
                $rule = $rules["proxy"];
                $rule["inboundTag"][] = $serverInTags[$i];
                $rule["outboundTag"] = $serverOutTags[$i];
                $this->routingRuleFilter($rule, $result);
            }
        }
        if (isset($rules["direct"])) {
            $rule = $rules["direct"];
            $rule["inboundTag"] = $serverInTags;
            $rule["outboundTag"] = "direct";
            $this->routingRuleFilter($rule, $result);
        }
        return $result;
    }

    private function routingRuleFilter(array $rule, array &$result)
    {
        $rule_ip = array_diff_key($rule, ["domain" => []]);
        $rule_domain = array_diff_key($rule, ["ip" => []]);
        if (!empty($rule_ip["ip"])) {
            $result[] = $rule_ip;
        }
        if (!empty($rule_domain["domain"])) {
            $result[] = $rule_domain;
        }
    }
}
