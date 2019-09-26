<?php

namespace App\Http\Resources;

class ServerCollection extends ResourceCollectionCommon
{
    public $collects = \App\Http\Resources\ServerIndex::class;
}
