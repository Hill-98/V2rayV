<?php

namespace App\Exceptions\V2ray;

use App\Models\ErrorCode;
use Throwable;

class DeleteFail extends \Exception
{
    public function __construct($message = 'Delete fail.', $code = ErrorCode::DELETE_FAIL)
    {
        parent::__construct($message, $code, null);
    }
}
