<?php

namespace App\V2rayV\Validation;

class Setting extends Validation
{
    protected $configKeys = [
        "main_port" => "int",
        "main_http_port" => "int",
        "allow_lan" => "bool",
        "log_level" => "string",
        "auto_update_v2ray" => "bool",
        "update_v2ray_proxy" => "bool",
        "auto_start" => "bool"
    ];

    /**
     * 校验主服务器端口
     *
     * @param int $value
     * @return bool
     */
    protected function isMainPort(int $value): bool
    {
        return $this->validPort($value);
    }

    /**
     * 校验主服务器 HTTP 端口
     *
     * @param int $value
     * @return bool
     */
    protected function isMainHttpPort(int $value): bool
    {
        if ($value === 0) {
            return true;
        }
        return $this->validPort($value);
    }

    protected function isAllowLan()
    {
        return true;
    }

    protected function isLogLevel(string $value)
    {
        $list = [
            "debug",
            "info",
            "warning",
            "error",
            "none"
        ];
        return in_array($value, $list);
    }

    protected function isAutoUpdateV2ray()
    {
        return true;
    }

    protected function isUpdateV2rayProxy()
    {
        return true;
    }

    protected function isAutoStart()
    {
        return true;
    }
}
