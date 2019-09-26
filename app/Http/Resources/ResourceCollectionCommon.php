<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ResourceCollectionCommon extends ResourceCollection
{
    private $success;
    private $code;
    private $message;
    public function __construct($resource, bool $success = true, int $code = 0, string $message = '')
    {
        $this->success = $success;
        $this->code = $code;
        $this->message = $message;
        parent::__construct($resource);
    }

    public function with($request): array
    {
        return [
            'success' => $this->success,
            'code' => $this->code,
            'message' => $this->message
        ];
    }
}
