<?php

namespace App\Jobs;

use App\Helper\V2ray;
use App\V2rayV\Config\Generate;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class V2rayControl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    private $token;
    private $code;

    /**
     * Create a new job instance.
     *
     * @param string $token
     * @param int $code
     */
    public function __construct(string $token, int $code = 0)
    {
        $this->token = $token;
        $this->code = $code;
    }

    /**
     * Execute the job.
     *
     * @param Generate $generate
     * @return void
     */
    public function handle(Generate $generate)
    {
        if ($this->code !== 0) {
            $this->control($this->code);
            return;
        }
        $cache_token = Cache::get("V2rayControl");
        if ($cache_token !== $this->token) {
            return;
        }
        $config = $generate();
        $this->control(V2ray::STOP);
        if (empty($config)) {
            return;
        }
        sleep(1);
        foreach ($config["log"] as $value) {
            if (file_exists($value)) {
                try {
                    unlink($value);
                } catch (\Exception $e) {
                }
            }
        }
        Storage::put("v2ray/config.json", json_encode($config, JSON_PRETTY_PRINT));
        $this->control(V2ray::START);
    }

    public function control($command)
    {
        Storage::put("v2ray.vvv", $command);
    }
}
