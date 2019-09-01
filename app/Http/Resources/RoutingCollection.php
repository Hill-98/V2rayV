<?php

namespace App\Http\Resources;

class RoutingCollection extends ResourceCollectionCommon
{
    public $collects = "App\Http\Resources\RoutingIndex";
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
