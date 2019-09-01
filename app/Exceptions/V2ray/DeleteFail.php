<?php

namespace App\Exceptions\V2ray;

use App\Models\ErrorCode;
use Throwable;

class DeleteFail extends \Exception
{
    public function __construct($message = "Delete fail.", $code = ErrorCode::DELETE_FAIL, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
