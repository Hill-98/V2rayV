<?php

namespace App\Helper;

use App\V2rayV\Setting;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

class Network
{
    private $setting;

    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }

    public function getProxyUrl(bool $use): string
    {
        $proxy_url = '';
        if (!$use) {
            return $proxy_url;
        }
        try {
            $context = Storage::get('v2ray.vvv');
            if ((int)$context === V2ray::START) {
                $proxy_url = "socks://127.0.0.1:{$this->setting->main_port}";
            }
        } catch (FileNotFoundException $e) {
        }
        return $proxy_url;
    }
}
