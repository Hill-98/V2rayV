<?php
declare(strict_types=1);

namespace App\V2rayV;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

class DefaultRouting
{
    private $default = [
        "proxy" => [],
        "direct" => [],
        "block" => [],
        "port" => "",
        "network" => "",
        "protocol" => [],
    ];
    private $routingModel;
    private $routingPath = "config/routing.json";

    /**
     * DefaultRouting constructor.
     * @param Routing $routing
     */
    public function __construct(Routing $routing)
    {
        $this->routingModel = $routing;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        try {
            return json_decode(Storage::get($this->routingPath), true);
        } catch (FileNotFoundException $e) {
            return $this->default;
        }
    }

    /**
     * @param array $config
     * @return bool
     * @throws \App\Exceptions\V2ray\ValidationException
     */
    public function put(array $config): bool
    {
        $config["servers"] = ["all"];
        $this->routingModel->valid($config);
        unset($config["servers"]);
        return Storage::put($this->routingPath, json_encode($config, JSON_PRETTY_PRINT));
    }

}
