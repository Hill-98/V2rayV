<?php

namespace App\Exceptions\V2ray;

use App\Models\ErrorCode;
use Throwable;

class DataSaveFail extends \Exception
{
    public function __construct($message = 'Data save fail.', $code = ErrorCode::DATA_SAVE_FAIL)
    {
        parent::__construct($message, $code);
    }
}
