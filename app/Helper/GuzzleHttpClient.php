<?php

namespace App\Helper;

use GuzzleHttp\Client;

class GuzzleHttpClient extends Client
{
    public function __construct(array $config = [])
    {
        $config["verify"] = base_path() . "/php/cacert.pem";
        parent::__construct($config);
    }
}
