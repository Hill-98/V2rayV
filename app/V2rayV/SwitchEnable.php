<?php

namespace App\V2rayV;

use App\Exceptions\V2ray\NotExist;
use Illuminate\Database\Eloquent\Model;

trait SwitchEnable
{
    /**
     * @param array $list
     * @param bool $enable
     * @return array
     */
    public function switch(array $list, bool $enable): array
    {
        $result = [];
        foreach ($list as $id) {
            if (!is_int($id)) {
                continue;
            }
            try {
                /** @var Model $model */
                $model = parent::get($id);
                $model->enable = $enable;
                $model->save();
                $result[] = $id;
            } catch (NotExist $e) {
            }
        }
        return $result;
    }
}
