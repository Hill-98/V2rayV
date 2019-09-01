<?php

namespace App\Exceptions\V2ray;

use App\Models\ErrorCode;
use Throwable;

class NotExist extends \Exception
{
    public function __construct($message = "Not Exist.", $code = ErrorCode::NOT_EXIST, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
