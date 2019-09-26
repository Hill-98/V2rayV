<?php

namespace App\Http\Resources;

class SubscribeCollection extends ResourceCollectionCommon
{
    public $collects = \App\Http\Resources\SubscribeIndex::class;
}
