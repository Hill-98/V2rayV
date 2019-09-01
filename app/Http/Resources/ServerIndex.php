<?php

namespace App\Http\Resources;

/**
 * Class ServerIndex
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Server
 */
class ServerIndex extends ResourceCommon
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "address" => $this->address,
            "port" => $this->port,
            "local_port" => $this->local_port,
            "protocol" => $this->protocol,
            "enable" => $this->enable,
            "subscribe" => [
                "name" => $this->subscribe->name ?? "",
            ],
        ];
    }
}
