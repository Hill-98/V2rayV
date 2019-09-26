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
        if (empty($request->input('filter'))) {
            return $result;
        }
        $filters = explode(',', $request->input('filter'));
        foreach ($filters as $filter) {
            $filter = trim($filter);
            if ($filter !== '') {
                $result->filter[] = $filter;
                // 过滤器参数通过过滤器名称对应的查询字符串
                $filter_value = $request->input($filter);
                if ($filter !== null) {
                    $result->filer_value[$filter] = $filter_value;
                }
            }
        }
        return $result;
    }
}
