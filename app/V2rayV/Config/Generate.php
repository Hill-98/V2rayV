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
     * 启用 HTTP 代理
     *
     * @var bool $enableHttpProxy
     */
    private $enableHttpProxy;
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
     * @return array
     */
    public function __invoke(): array
    {
        if ($this->serverModel->list(false)->count() === 0) {
            return [];
        }
        // 导入默认配置
        $this->config = include(__DIR__ . '/config.sample.php');
        $this->mainServer = $this->setting->main_server;
        $this->serverList = $this->serverModel->list(false, ['enable']);
        $this->routingList = $this->routingModel->list(false, ['enable']);
        // 如果主服务器不存在，选择第一个服务器当作主服务器。
        try {
            $server = $this->serverModel->get($this->mainServer);
        } catch (NotExist $e) {
            $server = $this->serverModel->list(false)->first();
            $this->mainServer = $server->id;
        }
        if ($this->serverList->where('id', $server->id)->isEmpty()) {
            $this->serverList->push($server);
        }
        if ($this->serverList->count() === 0) {
            return [];
        }
        $this->config['log'] = $this->logConfig();
        $this->config['dns'] = $this->dnsConfig();
        if ($this->config['dns'] === []) {
            unset($this->config['dns']);
        }
        $this->enableHttpProxy = $this->setting->main_http_port !== 0;
        $this->config['inbounds'] = array_merge(
            $this->config['inbounds'],
            $this->base->generateInboundsClient(
                $this->serverList,
                false,
                $this->mainServer,
                $this->setting->main_port,
                $this->setting->main_http_port
            )
        );
        $this->config['outbounds'] = array_merge(
            $this->config['outbounds'],
            $this->base->generateOutbounds($this->serverList, $this->mainServer)
        );
        $this->config['routing']['rules'] = array_merge($this->config['routing']['rules'], $this->routingRules());
        if ($this->setting->allow_lan) {
            foreach ($this->config['inbounds'] as &$inbound) {
                $protocol = $inbound['protocol'];
                if ($protocol === 'socks' || $protocol === 'http') {
                    $inbound['listen'] = '0.0.0.0';
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
            'access' => (new AccessLog())->getPath(true),
            'error' => (new ErrorLog())->getPath(true),
            'loglevel' => $this->setting->log_level
        ];
        return $config;
    }

    /**
     * 生成 DNS 配置
     *
     * @return array
     */
    private function dnsConfig(): array
    {
        $config = [
            'hosts' => [],
            'servers' => [],
            'tag' => 'dns'
        ];
        $dnsData = (new Dns())->readFile();
        if (empty($dnsData)) {
            return [];
        }
        $dnsItems = explode(',', $dnsData);
        foreach ($dnsItems as $item) {
            $item = trim($item);
            if (empty($item)) {
                continue;
            }
            $data = explode(' ', $item);
            $count = count($data);
            if ($count === 1 && !Str::startsWith($data[0], '$')) { // DNS 服务器
                $config['servers'][] = $data[0];
            } elseif ($count >= 2 && Str::startsWith($data[0], '$')) { // 指定域名 DNS 服务器
                $dnsServer = explode('#', Str::after($data[0], '$'));
                $serverConfig = [
                    'address' => $dnsServer[0],
                    'port' => isset($dnsServer[1]) ? (int)$dnsServer[1] : 53,
                    'domains' => []
                ];
                unset($data[0]);
                foreach ($data as $value) {
                    $serverConfig['domains'][] = $value;
                }
                $config['servers'][] = $serverConfig;
            } elseif ($count === 2) { // Hosts
                if (filter_var($data[0], FILTER_VALIDATE_IP) === $data[0]) {
                    $config['hosts'][$data[1]] = $data[0];
                }
            }
        }
        if ($config['hosts'] === []) {
            $config['hosts'] = (object)[];
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
            'type' => 'field',
            'inboundTag' => [],
            'outboundTag' => ''
        ];
        $routingRules = [];
        /** @var \App\Models\Routing $routing */
        // 遍历已启用规则并生成
        foreach ($this->routingList as $routing) {
            $routingRules[] = $this->generateRules($routing->toArray());
        }
        $routingRules = array_merge([], ...$routingRules);
        // 默认规则生成
        $defaultRule = $this->defaultRouting->get();
        $routingRules = array_merge($routingRules, $this->generateRules($defaultRule, true));
        $serverRules = [];
        // 遍历服务器并设置代理规则
        foreach ($this->serverList as $server) {
            $serverRule = $ruleConfig;
            $serverRule['inboundTag'] = $this->generateTag($server->id, false);
            $serverRule['outboundTag'] = $this->generateTag($server->id, true);
            // 当前服务器是否有自定义代理规则
            if (isset($this->customRuleServers[$server->id])) {
                $rule = $this->customRuleServers[$server->id];
                $serverRule['network'] = $rule['network'];
                if (!empty($rule['port'])) {
                    $serverRule['port'] = $rule['port'];
                }
                // 如果设置了流量类型，启用对应入站连接的流量侦测
                if (!empty($rule['protocol'])) {
                    $serverRule['protocol'] = $rule['protocol'];
                    foreach ($this->config['inbounds'] as &$item) {
                        if ($item['tag'] === $serverRule['inboundTag'][0]) {
                            $item['sniffing']['enabled'] = true;
                        }
                    }
                    unset($item);
                }
            }
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
            'type' => 'field',
            'domain' => [],
            'ip' => [],
            'inboundTag' => [],
            'outboundTag' => ''
        ];
        $rules = [
            'proxy' => $ruleConfig,
            'direct' => $ruleConfig,
            'block' => $ruleConfig
        ];
        // 处理端口文本
        if (Str::endsWith($config['port'], ',')) {
            $config['port'] = substr($config['port'], 0, -1);
        }
        // 遍历代理、直连、阻止列表
        foreach ($rules as $key => &$item) {
            if (empty($config[$key])) {
                unset($rules[$key]);
            }
            foreach ($config[$key] as $value) {
                $ip = explode('/', $value)[0];
                // 是否属于 IP 列表的规则
                if (Str::startsWith($value, 'geoip:') || filter_var($ip, FILTER_VALIDATE_IP) === $ip) {
                    $item['ip'][] = $value;
                } elseif (Str::startsWith($value, 'iext:')) {
                    $item['ip'][] = Str::after($value, 'i');
                } else {
                    $item['domain'][] = $value;
                }
            }
        }
        unset($item);
        $servers = [];
        if ($default) {
            $servers[] = $this->mainServer;
        } elseif ($config['servers'][0] === 'all') {
            /** @var Server $server */
            foreach ($this->serverList as $server) {
                $servers[] = $server->id;
            }
        } else {
            foreach ($config['servers'] as $serverId) {
                // 服务器是否存在
                if (($serverId !== $this->mainServer) &&
                    $this->serverList->where('id', $serverId)->isNotEmpty()) {
                    $servers[] = (int)$serverId;
                }
            }
        }
        $serverInTags = [];
        $serverOutTags = [];
        // 遍历适用于规则的服务器列表，生成 TAG 和服务器代理规则配置
        foreach ($servers as $serverId) {
            // 是否已存在适用于当前服务器规则的配置
            $serverRule = $this->customRuleServers[$serverId] ?? [];
            /**
             * 网络类型、端口、协议 适用于整个服务器代理而不匹配 IP 和域名
             * 只有优先级更高的路由规则生效
             */
            if (!isset($serverRule['network'])) {
                $serverRule['network'] = empty(trim($config['network'])) ? 'tcp,udp' : $config['network'];
            }
            if (!empty($config['port'])) {
                if (empty($serverRule['port'])) {
                    $serverRule['port'] = $config['port'];
                } else {
                    $serverRule['port'] .= ",{$config['port']}";
                }
            }
            if (empty($serverRule['protocol']) && !empty($config['protocol'])) {
                $serverRule['protocol'] = $config['protocol'];
            }
            $this->customRuleServers[$serverId] = $serverRule;
            $serverInTags[] = $this->generateTag($serverId, false);
            $serverOutTags[] = $this->generateTag($serverId, true);
        }
        $serverInTags = array_merge([], ...$serverInTags);
        // 规则导出
        $result = [];
        // 导出匹配 IP 或域名的规则
        if (isset($rules['block'])) {
            $rule = $rules['block'];
            $rule['inboundTag'] = $serverInTags;
            $rule['outboundTag'] = 'block';
            $this->routingRuleFilter($rule, $result);
        }
        if (isset($rules['proxy'])) {
            for ($i = 0, $iMax = count($serverInTags); $i < $iMax; $i++) {
                $rule = $rules['proxy'];
                $rule['inboundTag'][] = $serverInTags[$i];
                $rule['outboundTag'] = $serverOutTags[$i];
                $this->routingRuleFilter($rule, $result);
            }
        }
        if (isset($rules['direct'])) {
            $rule = $rules['direct'];
            $rule['inboundTag'] = $serverInTags;
            $rule['outboundTag'] = 'direct';
            $this->routingRuleFilter($rule, $result);
        }
        return $result;
    }

    /**
     * 生成配置标签
     *
     * @param int $server
     * @param bool $out
     * @param bool $http
     * @return string|array
     */
    public function generateTag(int $server, bool $out, bool $http = true)
    {
        $prefix = "server-${server}-";
        if ($out) {
            return "${prefix}out";
        }
        $result = ["${prefix}in"];
        if ($server === $this->mainServer && $this->enableHttpProxy && $http) {
            $result[] = "${prefix}in-http";
        }
        return $result;
    }

    /**
     * 分离域名和IP路由规则
     *
     * @param array $rule
     * @param array $result
     * @return void
     */
    private function routingRuleFilter(array $rule, array &$result): void
    {
        $rule_ip = array_diff_key($rule, ['domain' => []]);
        $rule_domain = array_diff_key($rule, ['ip' => []]);
        if (!empty($rule_ip['ip'])) {
            $result[] = $rule_ip;
        }
        if (!empty($rule_domain['domain'])) {
            $result[] = $rule_domain;
        }
    }
}
