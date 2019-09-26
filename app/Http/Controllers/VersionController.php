<?php

namespace App\Http\Controllers;

use App\Helper\GuzzleHttpClient;
use App\Helper\Network;
use App\Helper\V2ray;
use App\Models\ErrorCode;
use App\V2rayV\Setting;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class VersionController extends Controller
{
    private $network_helper;
    private $v2ray_helper;
    private $setting;

    /**
     * VersionController constructor.
     * @param Network $network
     * @param V2ray $v2ray
     * @param Setting $setting
     */
    public function __construct(Network $network, V2ray $v2ray, Setting $setting)
    {
        $this->network_helper = $network;
        $this->v2ray_helper = $v2ray;
        $this->setting = $setting;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(): \Illuminate\Http\JsonResponse
    {
        return Response::result(true, 0, '', [
            'vvv' => $this->getVersion(),
            'v2ray' => $this->v2ray_helper->getVersion()
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUpdate(Request $request): \Illuminate\Http\JsonResponse
    {
        $version = $this->getVersion();
        $vvv_check_update = [
            'is_update' => false,
            'curr_version' => $version,
            'new_version' => $version
        ];
        $client = new GuzzleHttpClient([
            'base_uri' => 'https://api.github.com',
            'proxy' => $this->network_helper->getProxyUrl($this->setting->update_v2ray_proxy)
        ]);
        try {
            $response = $client->get('/repos/Hill-98/V2rayV/releases/latest');
            $json = json_decode($response->getBody()->getContents(), true);
            if (!empty($json['tag_name']) && $json['tag_name'] !== $vvv_check_update['curr_version']) {
                $vvv_check_update['is_update'] = true;
                $vvv_check_update['new_version'] = $json['tag_name'];
            }
        } catch (RequestException $e) {
            if ($e->getCode() !== 404) {
                $vvv_check_update = false;
            }
        }
        $v2ray_check_update = true;
        if ($request->input('check') !== '1') {
            $v2ray_check_update = $this->v2ray_helper->checkUpdate();
        }
        if (!$vvv_check_update || !$v2ray_check_update) {
            return Response::result(false, ErrorCode::CHECK_UPDATE_FAIL);
        }
        $data =  [
            'vvv' => $vvv_check_update
        ];
        if (isset($v2ray_check_update)) {
            $data['v2ray'] = $v2ray_check_update;
        }
        return Response::result(true, 0, '', $data);
    }

    /**
     * @return string
     */
    private function getVersion(): string
    {
        $version_file = base_path('version');
        $version = 'null';
        if (file_exists($version_file)) {
            $version = explode("\n", file_get_contents($version_file))[0];
        }
        return $version;
    }
}
