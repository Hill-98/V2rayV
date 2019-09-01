<?php
declare(strict_types=1);

namespace App\V2rayV;

use App\Exceptions\V2ray\DataSaveFail;
use App\Exceptions\V2ray\NotExist;
use App\Exceptions\V2ray\Subscribe\SubscribeNameExist;
use App\Exceptions\V2ray\Subscribe\SubscribeURLExist;
use App\Exceptions\V2ray\ValidationException;
use App\Models\Subscribe as Model;
use App\V2rayV\Validation\Subscribe as Validation;

class Subscribe extends Data
{
    protected $dataCol = [
        "name",
        "url",
        "mux",
        "password",
        "auto_update",
        "proxy_update",
    ];
    protected $filterRule = [
        "auto_update" => [
            "col" => "auto_update"
        ]
    ];
    /** @var Model  */
    protected $model = Model::class;
    protected $validation = Validation::class;


    /**
     * @param array $data
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @return int
     * @throws SubscribeNameExist
     * @throws SubscribeURLExist
     * @throws ValidationException
     * @throws DataSaveFail
     */
    protected function save(array $data, \Illuminate\Database\Eloquent\Model $model = null): int
    {
        if ($model === null) {
            try {
                $this->getForName($data["name"]);
                throw new SubscribeNameExist();
            } catch (NotExist $e) {
            }
            try {
                $this->getForUrl($data["url"]);
                throw new SubscribeURLExist();
            } catch (NotExist $e) {
            }
        }
        $data["auto_update"] = !empty($data["auto_update"]);
        $data["proxy_update"] = !empty($data["proxy_update"]);
        return parent::save($data, $model);
    }

    /**
     * @param string $name
     * @return Model
     * @throws NotExist
     */
    private function getForName(string $name)
    {
        $subscribe = $this->model::whereName($name)->get()->first();
        if (empty($subscribe)) {
            throw new NotExist();
        }
        return $subscribe;
    }

    /**
     * @param string $url
     * @return Model
     * @throws NotExist
     */
    private function getForUrl(string $url)
    {
        $subscribe = $this->model::whereUrl($url)->get()->first();
        if (empty($subscribe)) {
            throw new NotExist();
        }
        return $subscribe;
    }
}
