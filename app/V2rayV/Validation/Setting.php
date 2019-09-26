<?php

namespace App\V2rayV\Validation;

class Setting extends Validation
{
    protected $configKeys = [
        'main_server' => 'int',
        'main_port' => 'int',
        'main_http_port' => 'int',
        'allow_lan' => 'bool',
        'log_level' => 'string',
        'auto_update_v2ray' => 'bool',
        'update_v2ray_proxy' => 'bool',
        'auto_start' => 'bool'
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

    /**
     * 检查 V2ray 日志等级
     *
     * @param string $value
     * @return bool
     */
    protected function isLogLevel(string $value): bool
    {
        $list = [
            'debug',
            'info',
            'warning',
            'error',
            'none'
        ];
        return in_array($value, $list, true);
    }
}
