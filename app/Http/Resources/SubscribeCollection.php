<?php

namespace App\Http\Resources;

class SubscribeCollection extends ResourceCollectionCommon
{
    public $collects = "App\Http\Resources\SubscribeIndex";

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
