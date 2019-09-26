<?php
declare(strict_types=1);

namespace App\V2rayV\Validation;

class Subscribe extends Validation
{
    use IsMux;

    protected $configKeys = [
        'name' => 'string',
        'url' => 'string',
        'mux' => 'array',
    ];

    /**
     * 检查名字是否合法
     *
     * @param string $value
     * @return bool
     */
    protected function isName(string $value): bool
    {
        return !empty(trim($value));
    }

    /**
     * 检查 URL 是否i合法
     *
     * @param string $value
     * @return bool
     */
    protected function isUrl(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) === $value;
    }
}
