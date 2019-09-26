<?php

namespace App\V2rayV\Config;

use App\Models\Server;
use Illuminate\Support\Str;

class Base
{
    private $protocol;

    public function __construct()
    {
        $this->protocol = new Protocol();
    }

    /**
     * 获取指定网络类型的配置键
     *
     * @param string $network
     * @return string
     */
    private function networkSettingKey(string $network): string
    {
        $keyList = [];
        return $keyList[$network] ?? "${network}Settings";
    }

    /**
     * @param Server $server
     * @param bool $client
     * @return array
     */
    private function generateStreamSetting(Server $server, bool $client): array
    {
        $setting = [
            'network' => 'tcp',
            'security' => 'none',
            'tlsSettings' => []
        ];
        $setting['network'] = $server->network;
        $setting['security'] = $server->security;
        $network_setting = $server->network_setting;
        if ($server->network === 'tcp' && !$client && isset($network_setting['request'])) {
            unset($network_setting['request']);
        }
        $setting[$this->networkSettingKey($server->network)] = $network_setting;
        if (empty($server->security_setting)) {
            unset($setting['tlsSettings']);
        } else {
            $setting['tlsSettings'] = $server->security_setting;
        }
        return $setting;
    }

    /**
     * 给客户端生成入站连接
     *
     * @param $servers
     * @param bool $otherClient
     * @param int $mainServer
     * @param int $mainPort
     * @param int $mainHttpPort
     * @return array
     */
    public function generateInboundsClient(
        $servers,
        bool $otherClient,
        int $mainServer = 0,
        int $mainPort = 0,
        int $mainHttpPort = 0
    ): array {
        $socks = [
            'udp' => true,
//            "userLevel" => 0,
        ];
        $inbound = [
            'port' => 0,
            'listen' => '127.0.0.1',
            'protocol' => 'socks',
            'settings' => $socks,
            'tag' => '',
            'sniffing' => [
                'enabled' => false,
            ]
        ];
        $inbounds = [];
        $ports = [];
        /** @var Server $server */
        foreach ($servers as $server) {
            $serverId = $server->id;
            $inboundClone = $inbound;
            if (!$otherClient && $serverId === $mainServer) {
                $port = $mainPort;
            } else {
                // 不是主服务器的话本地端口不能为 0
                $port = $server->local_port;
                if ($port === 0 && !$otherClient) {
                    continue;
                }
                if ($port === $mainPort || $port === $mainHttpPort) {
                    continue;
                }
                /**
                 * 如果是为客户端生成配置文件，本地端口是0或者本地端口和已有的重复，将本地端口改为随机生成。
                 * 因为有可能本地端口是 0 的服务器，随机的生成端口，会和已有的本地端口重复。
                 */
                if ($otherClient && ($port === 0 || in_array($port, $ports, true))) {
                    do {
                        try {
                            $port = random_int(10000, 65535);
                        } catch (\Exception $e) {
                        }
                    } while (in_array($port, $ports, true));
                }
            }
            $ports[] = $port;
            $tag = "server-${serverId}-in";
            $inboundClone['port'] = $port;
            $inboundClone['tag'] = $tag;
            $inbounds[] = $inboundClone;
        }
        if (!empty($mainHttpPort) && $mainPort !== $mainHttpPort) {
            $inbounds[] = [
                'port' => $mainHttpPort,
                'listen' => '127.0.0.1',
                'protocol' => 'http',
//                "settings" => (),
                'tag' => "server-${mainServer}-in-http",
                'sniffing' => [
                    'enabled' => false,
                ]
            ];
        }
        return $inbounds;
    }

    /**
     * 给服务器生成入站连接
     *
     * @param $servers
     * @return array
     */
    public function generateInboundsServer($servers): array
    {
        $inbound = [
            'port' => 0,
            'listen' => '0.0.0.0',
            'protocol' => '',
            'settings' => [],
            'streamSettings' => []
        ];
        $inbounds = [];
        /** @var Server $server */
        foreach ($servers as $server) {
            $inboundClone = $inbound;
            $inboundClone['port'] = $server->port;
            $inboundClone['protocol'] = $server->protocol;
            $method = Str::title($server->protocol) . 'Inbound';
            $protocol_setting = call_user_func([$this->protocol, $method], $server);
            $inboundClone['settings'] = $protocol_setting;
            $inboundClone['streamSettings'] = $this->generateStreamSetting($server, false);
            $inbounds[] = $inboundClone;
        }
        return $inbounds;
    }

    /**
     * 生成出站连接
     *
     * @param $servers
     * @param int $mainServer
     * @return array
     */
    public function generateOutbounds($servers, int $mainServer): array
    {
        $outbound = [
            'protocol' => '',
            'settings' => [],
            'tag' => '',
            'streamSettings' => [],
            'mux' => []
        ];
        $outbounds = [];
        /** @var Server $server */
        foreach ($servers as $server) {
            $serverId = $server->id;
            $outboundClone = $outbound;
            if ($mainServer !== 0 && $server->id !== $mainServer && $server->local_port === 0) {
                continue;
            }
            $tag = "server-${serverId}-out";
            $outboundClone['protocol'] = $server->protocol;
            $method = Str::title($server->protocol) . 'Outbound';
            $protocol_setting = call_user_func([$this->protocol, $method], $server);
            $outboundClone['settings'] = $protocol_setting;
            $outboundClone['streamSettings'] = $this->generateStreamSetting($server, true);
            $outboundClone['tag'] = $tag;
            $outboundClone['mux'] = $server->mux;
            $outbounds[] = $outboundClone;
        }
        return $outbounds;
    }
}
