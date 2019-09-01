<?php

namespace App\Http\Resources;

/**
 * Class Server
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Subscribe
 */
class SubscribeIndex extends ResourceCommon
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
            "is_encrypt" => !empty($this->password),
            "server_count" => $this->servers()->count(),
            "last_success" => $this->last_success,
            "update_at" => $this->update_at->timestamp
        ];
    }
}
