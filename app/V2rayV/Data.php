<?php
declare(strict_types=1);

namespace App\V2rayV;

use App\Exceptions\V2ray\DataSaveFail;
use App\Exceptions\V2ray\DeleteFail;
use App\Exceptions\V2ray\NotExist;
use App\Exceptions\V2ray\ValidationException;
use App\Helper\Database;
use App\V2rayV\Validation\Validation;
use Illuminate\Database\Eloquent\Model;

abstract class Data
{
    /**
     * 数据列
     *
     * @var array $dataCol
     */
    protected $dataCol = [];
    protected $filterRule = [];
    /**
     * 数据库模型
     *
     * @var Model|\Eloquent $model
     */
    protected $model;
    /**
     * 数据验证器
     *
     * @var Validation $validation
     */
    protected $validation;

    /**
     * 数据库助手
     *
     * @var Database $databaseHelper
     */
    protected $databaseHelper;

    /**
     * Data constructor.
     *
     * @param Database $databaseHelper
     */
    public function __construct(Database $databaseHelper)
    {
        $this->databaseHelper = $databaseHelper;
    }

    /**
     * @param  bool  $pagination 是否分页
     * @param  array $filters 过滤器
     * @param  array $filterParam 过滤器参数
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function list(bool $pagination, array $filters = [], array $filterParam = [])
    {
        $prePage = 20;
        $model = $this->model::query();
        if (!empty($this->filterRule) && !empty($filters)) {
            $model = $this->databaseHelper->filter($model, $this->filterRule, $filters, $filterParam);
        }
        if ($pagination) {
            return $model->paginate($prePage);
        }
        return $model->get();
    }

    /**
     * @param  int $id
     * @return Model
     * @throws NotExist
     */
    public function get(int $id): Model
    {
        $result = $this->model::find($id);
        if (empty($result)) {
            throw new NotExist();
        }
        return $result;
    }

    /**
     * @param  array $data
     * @throws ValidationException
     */
    public function valid(array $data): void
    {
        /**
         * @var Validation $valid
         */
        $valid = new $this->validation();
        $valid($data);
    }

    /**
     * @param  array      $data
     * @param  Model|null $model
     * @return int
     * @throws ValidationException
     * @throws DataSaveFail
     */
    protected function save(array $data, Model $model = null): int
    {
        $this->valid($data);
        if ($model === null) {
            $model = new $this->model();
            if (isset($data['id'])) {
                unset($data['id']);
            }
        } else {
            $data['id'] = $model->toArray()['id'];
        }
        foreach ($this->dataCol as $col) {
            if (isset($data[$col])) {
                $model->$col = $data[$col];
            }
        }
        try {
            $saveStatus = $model->save();
        } catch (\Exception $e) {
            throw new DataSaveFail();
        }
        return $saveStatus ? $model->toArray()['id'] : 0;
    }

    /**
     * @param  array $data
     * @return int
     * @throws ValidationException
     * @throws DataSaveFail
     */
    public function add(array $data): int
    {
        return $this->save($data);
    }


    /**
     * @param  array $data
     * @param  int   $id
     * @return int
     * @throws NotExist
     * @throws ValidationException
     * @throws DataSaveFail
     */
    public function update(array $data, int $id): int
    {
        return $this->save($data, $this->get($id));
    }

    /**
     * @param  int $id
     * @return int
     * @throws NotExist
     * @throws DeleteFail
     */
    public function delete(int $id): int
    {
        $model = $this->get($id);
        try {
            $model->delete();
            return $model->toArray()['id'];
        } catch (\Exception $e) {
            throw new DeleteFail();
        }
    }
}
