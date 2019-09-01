<?php
declare(strict_types=1);

namespace App\V2rayV;

use App\Exceptions\V2ray\DataSaveFail;
use App\Exceptions\V2ray\NotExist;
use App\Exceptions\V2ray\ValidationException;
use App\Models\Routing as Model;
use App\V2rayV\Validation\Routing as Validation;

class Routing extends Data
{
    use switchEnable;
    protected $dataCol = [
        "name",
        "proxy",
        "direct",
        "block",
        "port",
        "network",
        "protocol",
        "servers",
        "enable"
    ];
    protected $filterRule = [
        "enable" => [
            "col" => "enable",
            "value" => 1
        ],
        "disable" => [
            "col" => "enable",
            "value" => 0
        ]
    ];
    /** @var Model  */
    protected $model = Model::class;
    protected $validation = Validation::class;

    /**
     * @param array $data
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @return int
     * @throws ValidationException
     * @throws DataSaveFail
     */
    protected function save(array $data, \Illuminate\Database\Eloquent\Model $model = null): int
    {
        if (empty(trim($data["name"] ?? ""))) {
            $data["name"] = "Routing rule - " . ($this->model::count() + 1);
        }
        $data["enable"] = !empty($data["enable"]);
        return parent::save($data, $model);
    }
}
