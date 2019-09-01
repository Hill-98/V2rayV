<?php

namespace App\Exceptions\V2ray\Subscribe;

use App\Models\ErrorCode;
use Exception;
use Throwable;

class SubscribeNameExist extends Exception
{
    public function __construct($message = "Subscribe name already exists.", $code = ErrorCode::SUBSCRIBE_NAME_EXIST, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
