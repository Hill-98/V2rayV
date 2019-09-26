<?php
declare(strict_types=1);

namespace App\Helper\V2rayN;

use App\Exceptions\V2ray\ShareURL\ResolveException;
use App\Models\Server;
use Illuminate\Support\Str;

class ShareURL
{
    /**
     * 解码 V2rayN 的分享链接
     *
     * @param string $url
     * @return array
     * @throws ResolveException
     */
    public function decode(string $url): array
    {
        $protocol_setting = [
            'vmess' => [
                'id' => '',
                'alterId' => 0,
                'security' => 'auto'
            ],
            'shadowsocks' => [
                'method' => '',
                'password' => '',
                'ota' => false
            ],
            'socks' => [
                'user' => '',
                'pass' => ''
            ]
        ];
        $network_setting = [
            'tcp' => [
                'header' => [
                    'type' => 'none'
                ]
            ],
            'kcp' => [
                'mtu' => 1460,
                'tti' => 50,
                'uplinkCapacity' => 5,
                'downlinkCapacity' => 5,
                'congestion' => false,
                'readBufferSize' => 2,
                'writeBufferSize' => 2,
                'header' => [
                    'type' => 'none'
                ]
            ],
            'ws' => [
                'path' => '/',
            ],
            'http' => [
                'host' => [],
                'path' => '/'
            ],
            'quic' => [
                'security' => 'none',
                'header' => [
                    'type' => 'none'
                ]
            ]
        ];
        $server = [
            'name' => '',
            'address' => '',
            'port' => '',
            'protocol' => '',
            'protocol_setting' => [],
            'network' => 'tcp',
            'network_setting' => [
                'header' => [
                    'type' => 'none'
                ]
            ],
            'security' => 'none',
            'security_setting' => [
                'alpn' => [
                    'http/1.1'
                ],
                'allowInsecure' => true
            ],
            'mux' => [
                'enabled' => false,
            ],
            'enable' => false,
        ];
        if (Str::startsWith($url, 'vmess://')) {
            $url = Str::after($url, 'vmess://');
            $json = json_decode(base64_decode($url), true);
            if (!$json || !$this->checkJSon($json)) {
                throw new ResolveException();
            }
            $protocol_setting['vmess']['id'] = $json['id'];
            $protocol_setting['vmess']['alterId'] = (int)$json['aid'];
            $server['name'] = $json['ps'];
            $server['address'] = $json['add'];
            $server['port'] = (int)$json["port"];
            $server['protocol'] = 'vmess';
            $server['protocol_setting'] = $protocol_setting['vmess'];
            $network = $json['net'];
            if ($network === 'h2') {
                $network = 'http';
            }
            $server['network'] = $network;
            $server['security'] = empty($json['tls']) ? 'none' : $json['tls'];
            switch ($network) {
                case 'tcp':
                    if ($json['type'] === 'http') {
                        $network_setting[$network]['header']['type'] = 'http';
                        $network_setting[$network]['header']['request'] = [
                            'version' => '1.1',
                            'method' => 'GET',
                            'path' => ['/'],
                        ];
                        $hosts = explode(',', $json['host']);
                        if (count($hosts) === 0) {
                            break;
                        }
                        $network_setting[$network]['header']['request']['headers'] = [
                            'Host' => []
                        ];
                        $network_setting[$network]['header']['request']['headers']['Host'] = $hosts;
                    }
                    break;
                case 'kcp':
                    $network_setting[$network]['header']['type'] = $json['type'];
                    break;
                case 'ws':
                    if (!empty($json['host'])) {
                        $network_setting[$network]['headers']['Host'] = $json['host'];
                    }
                    if (!empty($json['path'])) {
                        $network_setting[$network]['path'] = $json['path'];
                    }
                    break;
                case 'http':
                    $hosts = explode(',', $json['host']);
                    if (count($hosts) === 0) {
                        break;
                    }
                    if (!empty($json['path'])) {
                        $network_setting[$network]['path'] = $json['path'];
                    }
                    $network_setting[$network]['host'] = $hosts;
                    break;
                case 'quic':
                    if (!empty($json['host'])) {
                        $network_setting[$network]['security'] = $json['host'];
                    }
                    if (!empty($json['path'])) {
                        $network_setting[$network]['key'] = $json['path'];
                    }
                    $network_setting[$network]['header']['type'] = $json['type'];
            }
            $server['network_setting'] = $network_setting[$network];
        } elseif (Str::startsWith($url, 'ss://')) {
            $url = Str::after($url, 'ss://');
            // 取别名
            $name = Str::after($url, '#');
            if (!empty($name)) {
                $server['name'] = $name;
            }
            $server['protocol'] = 'shadowsocks';
            // 去除链接中的别名解码
            $value = base64_decode(Str::before($url, '#'));
            if (!$value) {
                throw new ResolveException();
            }
            $value = explode(':', $value);
            if (count($value) !== 3) {
                throw new ResolveException();
            }
            // 分割地址和密码
            $password = explode('@', $value[1]);
            if (count($password) !== 2) {
                throw new ResolveException();
            }
            $protocol_setting['shadowsocks']['method'] = $value[0];
            $protocol_setting['shadowsocks']['password'] = $password[0];
            $server['address'] = $password[1];
            $server['port'] = (int)$value[2];
            $server['protocol_setting'] = $protocol_setting['shadowsocks'];
        } elseif (Str::startsWith($url, 'socks://')) {
            $url = Str::after($url, 'socks://');
            $server['protocol'] = 'socks';
            $name = Str::after($url, '#');
            if (!empty($name)) {
                $server['name'] = $name;
            }
            $value = base64_decode(Str::before($url, '#'));
            if (!$value) {
                throw new ResolveException();
            }
            $value = explode(':', $value);
            if (count($value) !== 2) {
                throw new ResolveException();
            }
            $server['address'] = $value[0];
            $server['port'] = (int)$value[1];
            $server['protocol_setting'] = $protocol_setting['socks'];
        } else {
            throw new ResolveException();
        }
        return $server;
    }

    /**
     * 编码 V2rayN 分享链接
     *
     * @param Server $server
     * @return string
     */
    public function encode(Server $server): string
    {
        $vmess_data = [
            'v' => '2',
            'ps' => '',
            'add' => '',
            'port' => '',
            'id' => '',
            'aid' => '',
            'net' => '',
            'type' => '',
            'host' => '',
            'path' => '',
            'tls' => ''
        ];
        $url = '';
        switch ($server->protocol) {
            case 'vmess':
                $url = 'vmess://';
                $vmess_data['ps'] = $server->name;
                $vmess_data['add'] = $server->address;
                $vmess_data['port'] = $server->port;
                $vmess_data['id'] = $server->protocol_setting['id'];
                $vmess_data['aid'] = $server->protocol_setting['alterId'];
                $vmess_data['net'] = $server->network === 'http' ? 'h2' : $server->network;
                $vmess_data['tls'] = $server->security;
                switch ($server->network) {
                    case 'tcp':
                        $vmess_data['type'] = $server->network_setting['header']['type'];
                        if (!empty($server->network_setting['header']['request']['headers']['Host'])) {
                            $vmess_data['host'] = implode(
                                ',',
                                $server->network_setting['header']['request']['headers']['Host']
                            );
                        }
                        break;
                    case 'kcp':
                        $vmess_data['type'] = $server->network_setting['header']['type'];
                        break;
                    case 'ws':
                        $vmess_data['path'] = $server->network_setting['path'];
                        if (!empty($server->network_setting['headers']['Host'])) {
                            $vmess_data['host'] = $server->network_setting['headers']['Host'];
                        }
                        break;
                    case 'http':
                        $vmess_data['host'] = implode(',', $server->network_setting['host']);
                        $vmess_data['path'] = $server->network_setting['path'];
                        break;
                    case 'quic':
                        $vmess_data['host'] = $server->network_setting['security'];
                        if ($server->network_setting['security'] !== 'none') {
                            $vmess_data['path'] = $server->network_setting['key'];
                        }
                        $vmess_data['type'] = $server->network_setting['header']['type'];
                }
                $url_data = json_encode($vmess_data);
                $url .= base64_encode($url_data);
                break;
            case 'shadowsocks':
                $url = 'ss://';
                $url_data = sprintf(
                    '%s:%s@%s:%s',
                    $server->protocol_setting['method'],
                    $server->protocol_setting['password'],
                    $server->address,
                    $server->port
                );
                $url .= base64_encode($url_data) . '#' . $server->name;
                break;
            case 'socks':
                $url = 'socks://';
                $url_data = sprintf('%s:%s', $server->address, $server->port);
                $url .= base64_encode($url_data) . '#' . $server->name;
                break;
        }
        return $url;
    }

    /**
     * 检查 V2rayN 分享链接解码后的 JSON
     *
     * @param array $json
     * @return bool
     */
    private function checkJSon(array $json): bool
    {
        $keys = [
            'v',
            'ps',
            'add',
            'port',
            'id',
            'aid',
            'net',
            'type',
            'host',
            'path',
            'tls'
        ];
        foreach ($keys as $key) {
            if (!isset($json[$key]) || !is_string($json[$key])) {
                return false;
            }
        }
        return true;
    }
}
