<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ResourceCollectionCommon extends ResourceCollection
{
    private $success;
    private $code;
    private $message;
    public function __construct($resource, bool $success = true, int $code = 0, string $message = "")
    {
        $this->success = $success;
        $this->code = $code;
        $this->message = $message;
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    public function with($request)
    {
        return [
            "success" => $this->success,
            "code" => $this->code,
            "message" => $this->message
        ];
    }
}
