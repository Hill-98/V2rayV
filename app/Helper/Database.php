<?php
declare(strict_types=1);

namespace App\Helper;

use Illuminate\Database\Query\Builder;

/**
 * Class Database
 * @package App\Helper
 */

class Database
{
    /**
     * @param Builder $model
     * @param array $filter_rule
     * @param array $filters
     * @param array $filter_param
     * @return Builder
     */
    public function filter($model, array $filter_rule, array $filters, array $filter_param = [])
    {
        if (empty($filter_rule) || empty($filters)) {
            return $model;
        }
        for ($i = 0; $i < count($filters); $i++) {
            $filter = $filters[$i];
            // 如果指定过滤器没有规则，或指定规则没有对应列，则跳过。
            if (empty($filter_rule[$filter]) || empty($filter_rule[$filter]["col"])) {
                continue;
            }
            $col = $filter_rule[$filter]["col"];
            // 指定规则是否有默认值
            if (isset($filter_rule[$filter]["value"])) {
                $value = $filter_rule[$filter]["value"];
            } else {
                if (!isset($filter_param[$filter])) {
                    continue;
                }
                $value = $filter_param[$filter];
            }
            $model = $model->where($col, $value);
        }
        return $model;
    }
}
