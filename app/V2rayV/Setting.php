<?php

namespace App\V2rayV;

use App\Exceptions\V2ray\ValidationException;
use App\V2rayV\File\Setting as File;
use App\V2rayV\Validation\Setting as Validation;
use Illuminate\Support\Facades\Storage;

/**
 * Class Setting
 * @package App\V2rayV
 * @property int main_port
 * @property int main_http_port
 * @property bool allow_lan
 * @property string log_level
 * @property bool auto_update_v2ray
 * @property bool update_v2ray_proxy
 * @property bool auto_start
 */
class Setting
{
    private $config = [
        "main_server" => 1,
        "main_port" => 10866,
        "main_http_port" => 10668,
        "allow_lan" => false,
        "log_level" => "warning",
        "auto_update_v2ray" => true,
        "update_v2ray_proxy" => false,
        "auto_start" => false,
    ];

    private $setting_file;

    public function __construct(File $setting_file)
    {
        $this->setting_file = $setting_file;
        $config = $this->setting_file->readFile();
        if (!empty($config) && $config = json_decode($config, true)) {
            $this->config = $config;
        }
    }

    public function __get($name)
    {
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }
        return null;
    }

    /**
     * @param array $config
     * @throws \App\Exceptions\V2ray\ValidationException
     */
    private function valid(array $config)
    {
        return (new Validation())($config);
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return $this->config;
    }

    /**
     * @return int
     */
    public function getMainServer(): int
    {
        return $this->config["main_server"];
    }

    /**
     * @param array $config
     * @return bool
     * @throws \App\Exceptions\V2ray\ValidationException
     */
    public function save(array $config): bool
    {
        $this->valid($config);
        if ($config["auto_start"] !== $this->config["auto_start"]) {
            Storage::put("boot.vvv", var_export($config["auto_start"], true));
        }
        return $this->setting_file->writeFile(json_encode($config, JSON_PRETTY_PRINT));
    }

    /**
     * @param int $id
     * @return bool
     */
    public function setMainServer(int $id): bool
    {
        $this->config["main_server"] = $id;
        try {
            return $this->save($this->config);
        } catch (ValidationException $e) {
            return false;
        }
    }
}
