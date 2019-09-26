<?php

namespace App\Exceptions\V2ray\ShareURL;

use App\Models\ErrorCode;
use Throwable;

class ResolveException extends \Exception
{
    public function __construct($message = 'Share URL resolve fail.', $code = ErrorCode::SHARE_URL_RESOLVE)
    {
        parent::__construct($message, $code, null);
    }
}
