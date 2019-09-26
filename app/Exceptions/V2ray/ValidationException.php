<?php

namespace App\Exceptions\V2ray;

use App\Models\ErrorCode;

class ValidationException extends \Exception
{
    private $key;
    private $status;

    /**
     * ValidationException constructor.
     * @param string $key 校验异常键
     * @param string $status 校验失败状态
     * @param string $message
     */
    public function __construct($key = '', $status = '', $message = '')
    {
        $this->key = $key;
        $this->status = $status;
        parent::__construct($message, ErrorCode::VALIDATION_EXCEPTION, null);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }
}
