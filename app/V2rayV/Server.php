<?php

declare(strict_types=1);

namespace App\V2rayV;

use App\Exceptions\V2ray\AlreadyExists;
use App\Exceptions\V2ray\DataSaveFail;
use App\Exceptions\V2ray\NotExist;
use App\Exceptions\V2ray\ValidationException;
use App\Exceptions\V2ray\Server\ServerLocalPortExist;
use App\Models\Server as Model;
use App\V2rayV\Config\Base;
use App\V2rayV\Validation\Server as Validation;

class Server extends Data
{
    use SwitchEnable;

    protected $dataCol = [
        'name',
        'subscribe_id',
        'address',
        'port',
        'protocol',
        'protocol_setting',
        'network',
        'network_setting',
        'security',
        'security_setting',
        'mux',
        'local_port',
        'enable'
    ];
    protected $filterRule = [
        'enable' => [
            'col' => 'enable',
            'value' => 1
        ],
        'disable' => [
            'col' => 'enable',
            'value' => 0
        ],
        'address' => [
            'col' => 'address'
        ],
        'port' => [
            'col' => 'port',
        ],
        'subscribe_id' => [
            'col' => 'subscribe_id',
        ]
    ];
    /** @var Model  */
    protected $model = Model::class;
    protected $validation = Validation::class;

    /**
     * @param array $data
     * @param \Illuminate\Database\Eloquent\Model|null|Model $model
     * @return int
     * @throws ValidationException
     * @throws AlreadyExists
     * @throws ServerLocalPortExist
     * @throws DataSaveFail
     */
    protected function save(array $data, \Illuminate\Database\Eloquent\Model $model = null): int
    {
        if ($model === null) {
            $list = $this->list(false, ['address', 'port'], [
                'address' => $data['address'],
                'port' => (int)$data['port']
            ]);
            if ($list->isNotEmpty()) {
                throw new AlreadyExists();
            }
        }
        // 本地端口是否重复
        if (!empty($data['local_port'])) {
            $coll = $this->model::whereLocalPort($data['local_port'])->get();
            if ($coll->isNotEmpty()) {
                if ($model === null || $coll->first()->id !== $model->id) {
                    throw new ServerLocalPortExist();
                }
            }
        }
        $name = $data['name'] ?? '';
        if (!is_string($name) || empty(trim($name))) {
            $data['name'] = 'Server - ' . ($this->model::count() + 1);
        }
        if (isset($data['local_port'])) {
            $data['local_port'] = (int)$data['local_port'];
        }
        $data['enable'] = $data['enable'] ?? $model['enable'] ?? false;
        return parent::save($data, $model);
    }

    /**
     * 导出客户端/服务器配置
     *
     * @param array $servers
     * @param bool $client
     * @return array
     */
    public function exportConfig(array $servers, bool $client): array
    {
        foreach ($servers as $key => &$value) {
            if (!is_int($value)) {
                unset($servers[$key]);
                continue;
            }
            try {
                $value = $this->get($value);
            } catch (NotExist $e) {
                unset($servers[$key]);
            }
        }
        unset($value);
        if (empty($servers)) {
            return [];
        }
        $config = include(__DIR__ . '/Config/config.mini.php');
        $base = new Base();
        // 如果是客户端，需要生成入站出站以及路由规则，如果是服务器则只需要入站。
        if ($client) {
            $config['inbounds'] = $base->generateInboundsClient($servers, true);
            $config['outbounds'] = $base->generateOutbounds($servers, 0);
            $ruleConfig = [
                'type' => 'field',
                'inboundTag' => [],
                'outboundTag' => ''
            ];
            $routingRules = [];
            // 生成路由规则
            foreach ($config['inbounds'] as $inbound) {
                $rule = $ruleConfig;
                $rule['inboundTag'][] = $inbound['tag'];
                $rule['outboundTag'] = str_replace('in', 'out', $inbound['tag']);
                $routingRules[] = $rule;
            }
            $config['routing']['rules'] = $routingRules;
        } else {
            $config['inbounds'] = $base->generateInboundsServer($servers);
            unset($config['outbounds'], $config['routing']);
        }
        return $config;
    }
}
