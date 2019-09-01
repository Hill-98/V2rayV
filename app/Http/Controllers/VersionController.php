<?php

namespace App\Http\Controllers;

use App\Helper\Network;
use App\Helper\V2ray;
use App\Models\ErrorCode;
use App\V2rayV\Setting;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

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

    public function show()
    {
        return Response::result(true, 0, "", [
            "vvv" => $this->getVersion(),
            "v2ray" => $this->v2ray_helper->getVersion()
        ]);
    }

    public function checkUpdate(Request $request)
    {
//        $client = new Client([
//            "base_uri" => "https://api.github.com",
//            "proxy" => $this->network_helper->getProxyUrl($this->setting->update_v2ray_proxy)
//        ]);
        if ($request->input("check") !== "1") {
            $v2ray_check_update = $this->v2ray_helper->checkUpdate();
            if (!$v2ray_check_update) {
                return Response::result(false, ErrorCode::CHECK_UPDATE_FAIL);
            }
        }
        $vvv_is_update = false;
        $vvv_curr_version = $this->getVersion();
        $vvv_new_version = $this->getVersion();
        $data =  [
            "vvv" => [
                "is_update" => $vvv_is_update,
                "curr_version" => $vvv_curr_version,
                "new_version" => $vvv_new_version
            ],
        ];
        if (isset($v2ray_check_update)) {
            $data["v2ray"] = $v2ray_check_update;
        }
        return Response::result(true, 0, "", $data);
    }

    private function isWin64()
    {
        return PHP_INT_SIZE === 8;
    }

    private function getVersion(): string
    {
        $version_file = base_path("version");
        $version = "null";
        if (file_exists($version_file)) {
            $version = explode("\n", file_get_contents($version_file))[0];
        }
        return $version;
    }
}
