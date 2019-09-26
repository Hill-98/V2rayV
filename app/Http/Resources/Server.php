<?php

namespace App\Http\Resources;

/**
 * Class Server
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Server
 */
class Server extends ResourceCommon
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return array_merge(parent::toArray($request), [
            'subscribe' => [
                'name' => $this->subscribe->name ?? '',
                'id' => $this->subscribe->id ?? 0,
            ]
        ]);
    }
}
