<?php

namespace App\Exceptions\V2ray\Server;

use App\Models\ErrorCode;
use Throwable;

class ServerLocalPortExist extends \Exception
{
    public function __construct($message = 'Local port already exists.', $code = ErrorCode::V2RAY_LOCAL_PORT_EXIST)
    {
        parent::__construct($message, $code, null);
    }
}
