<?php

namespace App\Http\Resources;

/**
 * Class Routing
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Routing
 */
class Routing extends ResourceCommon
{
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
}
