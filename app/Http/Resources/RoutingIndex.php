<?php

namespace App\Http\Resources;

/**
 * Class RoutingIndex
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Routing
 */
class RoutingIndex extends ResourceCommon
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
            "port" => $this->port,
            "network" => $this->network,
            "protocol" => $this->protocol,
            "enable" => $this->enable,
        ];
    }
}
