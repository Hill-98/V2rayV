<?php

namespace App\Exceptions\V2ray\Subscribe;

use App\Models\ErrorCode;
use Exception;
use Throwable;

class SubscribeURLExist extends Exception
{
    public function __construct($message = "Subscribe url already exists.", $code = ErrorCode::SUBSCRIBE_URL_EXIST, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
