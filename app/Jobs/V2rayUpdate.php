<?php

namespace App\Jobs;

use App\Helper\Network;
use App\Helper\V2ray;
use App\V2rayV\Setting;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class V2rayUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    private $download_url;

    /**
     * Create a new job instance.
     *
     * @param string $download_url
     */
    public function __construct(string $download_url)
    {
        $this->download_url = $download_url;
    }

    /**
     * Execute the job.
     *
     * @param Network $network
     * @param Setting $setting
     * @return void
     */
    public function handle(Network $network, Setting $setting)
    {
        $client = new Client([
            "proxy" => $network->getProxyUrl($setting->update_v2ray_proxy)
        ]);
        try {
            $zip_path = Storage::path("v2ray.zip");
            $zip_file = fopen($zip_path, "w");
            $client->get($this->download_url, [
                "save_to" => $zip_file
            ]);
            $zip = new ZipArchive();
            if ($zip->open($zip_path, ZIPARCHIVE::CHECKCONS) === true) {
                V2rayControl::dispatchNow("", V2ray::STOP);
                sleep(1);
                $v2ray_dir = Storage::path("v2ray");
                if (Storage::exists("v2ray")) {
                    Storage::deleteDirectory("v2ray");
                }
                Storage::makeDirectory("v2ray");
                $zip->extractTo($v2ray_dir);
                $zip->close();
                event("V2rayControl");
            }
            unlink($zip_path);
        } catch (RequestException $e) {
            echo $e->getMessage();
        }
    }
}
