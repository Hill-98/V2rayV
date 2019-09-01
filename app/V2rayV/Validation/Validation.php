<?php
declare(strict_types=1);

namespace App\V2rayV\Validation;

use App\Exceptions\V2ray\ValidationException;
use Illuminate\Support\Str;

abstract class Validation
{
    /** @var array  */
    protected $configKeys = [];

    /**
     * @param array $config
     * @throws ValidationException
     */
    public function __invoke(array $config)
    {
        foreach ($this->configKeys as $key => $value) {
            if (!isset($config[$key])) {
                throw new ValidationException($key, "empty", "$key value cannot be empty");
            }
            $method = "is_${key}";
            if (Str::endsWith($key, "_setting")) {
                $type = Str::before($key, "_setting");
                if (isset($config[$type])) {
                    $method = "is_${config[$type]}-${type}";
                }
            }
            $method = Str::camel($method);
            if (!call_user_func("is_$value", $config[$key]) ||
                !call_user_func_array([$this, $method], [$config[$key]])) {
                throw new ValidationException($key, "invalid", "$key value invalid.");
            }
        }
    }

    /**
     * 端口是否正确
     *
     * @param int $value
     * @return bool
     */
    protected function validPort(int $value): bool
    {
        return ($value > 0 && $value <= 65535);
    }

    /**
     * Mux 配置是否正确
     *
     * @param array $config
     * @return bool
     */
    protected function isMux(array $config): bool
    {
        if (!isset($config["enabled"]) || !is_bool($config["enabled"])) {
            return false;
        }
        if ($config["enabled"]) {
            if (empty($config["concurrency"])) {
                return false;
            }
            $concurrency = $config["concurrency"];
            return (is_int($concurrency) && $concurrency >= 1 && $concurrency <= 1024);
        }
        return true;
    }
}
