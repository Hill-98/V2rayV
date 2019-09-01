<?php

namespace App\Http\Resources;

/**
 * Class Server
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Subscribe
 */
class Subscribe extends ResourceCommon
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $result = parent::toArray($request);
        $result["update_at"] = $this->update_at->timestamp;
        return $result;
    }
}
