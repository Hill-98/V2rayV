<?php
declare(strict_types=1);

namespace App\Helper;

use App\Models\DataFilter;
use Illuminate\Http\Request;

class Router
{
    /**
     * 从 URL 参数获取过滤器
     *
     * @param Request $request
     * @return DataFilter
     */
    public function getFilter(Request $request): DataFilter
    {
        $result = new DataFilter();
        // 过滤器通过 URL 传参，以逗号分割。
        if (empty($request->input("filter"))) {
            return $result;
        }
        $filters = explode(",", $request->input("filter"));
        foreach ($filters as $value) {
            $value = trim($value);
            if (!empty($value)) {
                $result->filter[] = $value;
                // 过滤器参数通过过滤器名称对应的查询字符串
                $filter_value = $request->input($value, null);
                if ($value !== null) {
                    $result->filer_value[$value] = $filter_value;
                }
            }
        }
        return $result;
    }
}
