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
     * @return void
     */
    public function __invoke(array $config): void
    {
        foreach ($this->configKeys as $key => $value) {
            if (!isset($config[$key])) {
                throw new ValidationException($key, 'empty', "$key value cannot be empty");
            }
            $method = "is_${key}";
            if (Str::endsWith($key, '_setting')) {
                $type = Str::before($key, '_setting');
                if (isset($config[$type])) {
                    $method = "is_${config[$type]}-${type}";
                }
            }
            $method = Str::camel($method);
            if (!call_user_func("is_$value", $config[$key]) ||
                (method_exists($this, $method) && !$this->$method($config[$key]))) {
                throw new ValidationException($key, 'invalid', "$key value invalid.");
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
}
