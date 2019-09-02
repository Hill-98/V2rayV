<?php

namespace App\Jobs;

use App\Exceptions\V2ray\NotExist;
use App\Helper\GuzzleHttpClient;
use App\Helper\Network;
use App\V2rayV\ShareURL;
use App\V2rayV\Subscribe;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SubscribeUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    private $subscribe_id;

    /**
     * Create a new job instance.
     *
     * @param int $subscribe_id
     * @param bool $controller
     */
    public function __construct(int $subscribe_id = 0)
    {
        $this->subscribe_id = $subscribe_id;
    }

    /**
     * Execute the job.
     *
     * @param Subscribe $Subscribe
     * @param ShareURL $shareURL
     * @param Network $network
     * @return void
     */
    public function handle(Subscribe $Subscribe, ShareURL $shareURL, Network $network)
    {
        if (empty($this->subscribe_id)) {
            $subscribe_list = $Subscribe->list(false, ["auto_update"], ["auto_update" => true]);
        } else {
            try {
                $subscribe_list = [$Subscribe->get($this->subscribe_id)];
            } catch (NotExist $e) {
                Log::warning("Subscript update error: Subscript not exist");
                return;
            }
        }

        /** @var \App\Models\Subscribe $subscribe */
        foreach ($subscribe_list as $subscribe) {
            $subscribe->update_at = now();
            $subscribe->last_success = false;
            try {
                $client = new GuzzleHttpClient([
                    "proxy" => $network->getProxyUrl($subscribe->proxy_update)
                ]);
                $response = $client->get($subscribe->url);
                $data = $response->getBody()->getContents();
                $data = base64_decode($data);
                if (!$data) {
                    Log::error($subscribe->name . " Subscript update error: data decoding failed");
                    continue;
                }
                $data = str_replace("\r\n", "\n", $data);
                $urls = explode("\n", $data);
                $password = "";
                /** @var Collection $servers_old */
                $servers_old = $subscribe->servers()->get();
                if (!empty($subscribe->password)) {
                    $password = $subscribe->password;
                }
                $add_result = $shareURL->import($urls, $password, $subscribe->id);
                $delete_count = 0;
                if ($servers_old->count() !== 0 && $add_result["fail"] === 0) {
                    // 遍历旧的服务器列表，如果旧服务器不存在于订阅列表则删除。
                    foreach ($servers_old as $server) {
                        if (!in_array($server->id, $add_result["servers"])) {
                            $server->delete();
                            $delete_count++;
                        }
                    }
                }
                $subscribe->last_success = true;
                Log::info($subscribe->name . " Subscript updated success！", [
                    "New" => $add_result["new"],
                    "Delete" => $delete_count,
                    "Fail" => $add_result["fail"],
                    "Total" => $add_result["total"]
                ]);
            } catch (RequestException $e) {
                Log::error($subscribe->name . " Subscript update error: Network request failed", [
                    "error" => $e->getMessage(),
                    "code" => $e->getCode()
                ]);
            }
            $subscribe->save();
        }
        event("V2rayControl");
    }
}
