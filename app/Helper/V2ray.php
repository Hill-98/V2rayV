<?php

namespace App\Helper;

use App\Jobs\V2rayUpdate;
use App\V2rayV\Setting;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Str;

class V2ray
{
    public const START = 1;
    public const STOP = 2;

    private $network_helper;
    private $setting;

    public function __construct(Network $network, Setting $setting)
    {
        $this->network_helper = $network;
        $this->setting = $setting;
    }

    public function checkUpdate()
    {
        $version = $this->getVersion();
        $result = [
            'is_update' => false,
            'curr_version' => $version,
            'new_version' => $version
        ];
        $client = new GuzzleHttpClient([
            'base_uri' => 'https://api.github.com',
            'proxy' => $this->network_helper->getProxyUrl($this->setting->update_v2ray_proxy)
        ]);
        try {
            $response = $client->get('/repos/v2ray/v2ray-core/releases/latest');
            $json = json_decode($response->getBody()->getContents(), true);
            if (!empty($json['tag_name']) && $json['tag_name'] !== "v${result['curr_version']}") {
                $result['is_update'] = true;
                $result['new_version'] = Str::after($json['tag_name'], 'v');
                $url = "https://github.com/v2ray/v2ray-core/releases/download/${json['tag_name']}/v2ray-windows-%s.zip";
                $url = sprintf($url, PHP_INT_SIZE === 4 ? '32' : '64');
                V2rayUpdate::dispatch($url);
            }
        } catch (RequestException $e) {
            return false;
        }
        return $result;
    }

    public function getVersion(): string
    {
        $v2ray_bin = storage_path('app/v2ray/v2ray.exe');
        $version = 'null';
        if (file_exists($v2ray_bin)) {
            exec("${v2ray_bin} --version", $output);
            if (preg_match("/(\d+\.)(\d+\.)?(\d+\.)?(\d+)/", $output[0], $matches)) {
                $version = $matches[0];
            }
        }
        return $version;
    }
}
