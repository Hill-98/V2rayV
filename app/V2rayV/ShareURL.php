<?php
declare(strict_types=1);

namespace App\V2rayV;

use App\Exceptions\V2ray\NotExist;
use App\Exceptions\V2ray\ShareURL\ResolveException;
use App\Exceptions\V2ray\ValidationException;
use \App\Helper\V2rayN\ShareURL as V2rayNShareURL;
use App\Models\Server as ServerModel;
use Illuminate\Support\Str;

class ShareURL
{
    private $serverModel;
    private $v2raynHelper;

    private $encrypt_method = "aes-256-ctr";

    public function __construct(Server $server, V2rayNShareURL $v2ray_n_helper)
    {
        $this->serverModel = $server;
        $this->v2raynHelper = $v2ray_n_helper;
    }

    /**
     * 导入分享 URL
     *
     * @param array $urls
     * @param string $password
     * @param int $subscribe_id
     * @return array
     */
    public function import(array $urls, string $password = "", int $subscribe_id = 0): array
    {
        $result = [
            "total" => count($urls),
            "new" => 0,
            "fail" => 0,
            "servers" => []
        ];
        foreach ($urls as $url) {
            if (empty($url) || empty(trim($url))) {
                $result["total"] --;
                continue;
            }
            // 判断是否是 URL 格式，如果不是，则可能是加密 URL。
            if (Str::is("*://*", $url)) {
                if (Str::startsWith($url, "vvv://")) {
                    $serverConfig = json_decode(base64_decode(Str::after($url, "vvv://")), true);
                } else {
                    try {
                        $serverConfig = $this->v2raynHelper->decode($url);
                    } catch (ResolveException $e) {
                        $result["fail"]++;
                        continue;
                    }
                }
            } else {
                // 解密数据
                $data = explode("::", base64_decode($url)); // 分割密文和 IV
                if (empty($password) || count($data) !== 2) {
                    $result["fail"]++;
                    continue;
                };
                $encryptData = $data[0];
                $iv = $data[1];
                $serverConfig = openssl_decrypt($encryptData, $this->encrypt_method, $password, OPENSSL_RAW_DATA, $iv);
                $serverConfig = json_decode($serverConfig, true);
            }
            if (!$serverConfig) {
                $result["fail"]++;
                continue;
            }
            // 验证解码后的数据是否正确
            try {
                $this->serverModel->valid($serverConfig);
            } catch (ValidationException $e) {
                $result["fail"]++;
                continue;
            }
            if (empty($subscribe_id)) {
                if (isset($serverConfig["subscribe_id"])) {
                    unset($serverConfig["subscribe_id"]);
                }
            } else {
                $serverConfig["subscribe_id"] = $subscribe_id;
            }
            $list = $this->serverModel->list(
                false,
                ["address", "port"],
                ["address" => $serverConfig["address"], "port" => intval($serverConfig["port"])]
            );
            try {
                // 是否是已存在服务器
                if ($list->isEmpty()) {
                    $serverId = $this->serverModel->add($serverConfig);
                    $result["new"]++;
                } else {
                    $serverId = $list->first()->id;
                    $this->serverModel->update($serverConfig, $serverId);
                }
                $result["servers"][] = $serverId;
            } catch (\Exception $e) {
                $result["fail"]++;
            }
        }
        return $result;
    }

    /**
     * 导出分享 URL
     *
     * @param array $serverId
     * @param bool $encrypt
     * @return array
     */
    public function export(array $serverId, bool $encrypt = false): array
    {
        $password = "";
        $result = [
            "vvv" => [],
            "V2rayN" => [],
        ];
        if ($encrypt) {
            $password = Str::random();
            $result["password"] = $password;
        }
        foreach ($serverId as $id) {
            if (!is_int($id)) {
                continue;
            }
            try {
                /** @var ServerModel $server */
                $server = $this->serverModel->get($id);
                $url = $this->encode($server, !$encrypt);
                if ($encrypt) {
                    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->encrypt_method));
                    $url = openssl_encrypt($url, $this->encrypt_method, $password, OPENSSL_RAW_DATA, $iv);
                    $url = base64_encode($url . "::$iv");
                }
                $result["vvv"][] = $url;
                $url = $this->v2raynHelper->encode($server);
                $result["V2rayN"][] = $url;
            } catch (NotExist $e) {
            }
        }
        return $result;
    }

    /**
     * 编码 URL 链接
     * @param ServerModel $server
     * @param bool $base64
     * @return string
     */
    private function encode(ServerModel $server, bool $base64): string
    {
        $server = $server->toArray();
        unset($server["id"]);
        unset($server["subscribe_id"]);
        unset($server["local_port"]);
        unset($server["enable"]);
        $data = json_encode($server);
        $result = $data;
        if ($base64) {
            $result = "vvv://" . base64_encode($data);
        }
        return $result;
    }
}
