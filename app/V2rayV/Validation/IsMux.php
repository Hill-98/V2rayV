<?php

namespace App\V2rayV\Validation;

trait IsMux
{
    /**
     * Mux 配置是否正确
     *
     * @param array $config
     * @return bool
     */
    protected function isMux(array $config): bool
    {
        if (!isset($config['enabled']) || !is_bool($config['enabled'])) {
            return false;
        }
        if ($config['enabled']) {
            if (empty($config['concurrency'])) {
                return false;
            }
            $concurrency = $config['concurrency'];
            return (is_int($concurrency) && $concurrency >= 1 && $concurrency <= 1024);
        }
        return true;
    }
}
