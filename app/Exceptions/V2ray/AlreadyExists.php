<?php

namespace App\Exceptions\V2ray;

use App\Models\ErrorCode;
use Throwable;

class AlreadyExists extends \Exception
{
    public function __construct($message = "Already exists.", $code = ErrorCode::ALREADY_EXISTS, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
