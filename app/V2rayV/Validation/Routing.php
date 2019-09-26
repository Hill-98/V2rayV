<?php
declare(strict_types=1);

namespace App\V2rayV\Validation;

use App\Exceptions\V2ray\NotExist;
use App\V2rayV\Server;

class Routing extends Validation
{
    protected $configKeys = [
        'proxy' => 'array',
        'direct' => 'array',
        'block' => 'array',
        'port' => 'string',
        'network' => 'string',
        'protocol' => 'array',
        'servers' => 'array'
    ];

    /**
     * 是否是字符串列表
     *
     * @param array $list
     * @return bool
     */
    protected function isStringList(array $list): bool
    {
        foreach ($list as $value) {
            if (!is_string($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 代理列表是否正确
     *
     * @param array $config
     * @return bool
     */
    protected function isProxy(array $config): bool
    {
        return $this->isStringList($config);
    }

    /**
     * 直连列表是否正确
     *
     * @param array $config
     * @return bool
     */
    protected function isDirect(array $config): bool
    {
        return $this->isStringList($config);
    }

    /**
     * 阻止列表是否正确
     *
     * @param array $config
     * @return bool
     */
    protected function isBlock(array $config): bool
    {
        return $this->isStringList($config);
    }

    /**
     * 端口范围是否正确
     *
     * @param string $config
     * @return bool
     */
    protected function isPort(string $config): bool
    {
        if (empty(trim($config))) {
            return true;
        }
        $portList = explode(',', $config);
        foreach ($portList as $value) {
            $arr = explode('-', $value);
            if (count($arr) > 2) {
                return false;
            }
            foreach ($arr as $port) {
                $port = (int)$port;
                if (!$this->validPort($port)) {
                    return false;
                }
            }
        }
        return true;
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
            'tcp',
            'udp',
            'tcp,udp'
        ];
        return in_array($value, $list, true);
    }

    /**
     * 协议列表是否正确
     *
     * @param array $config
     * @return bool
     */
    protected function isProtocol(array $config): bool
    {
        $list = [
            'http',
            'tls',
            'bittorrent'
        ];
        foreach ($config as $value) {
            if (!in_array($value, $list, true)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 服务器列表是否正确
     *
     * @param array $config
     * @return bool
     */
    protected function isServers(array $config): bool
    {
        if (!empty($config[0]) && $config[0] === 'all') {
            return true;
        }
        $server = app(Server::class);
        foreach ($config as $value) {
            if (!is_int($value)) {
                return false;
            }
            try {
                $server->get($value);
            } catch (NotExist $e) {
                return false;
            }
        }
        return true;
    }
}
