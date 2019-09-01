<?php
declare(strict_types=1);

namespace App\Helper;

class Path
{
    /**
     * 解析相对存储路径为绝对相对路径
     * 支持解析为 Windows 标准格式
     *
     * @param string $path
     * @param bool $windows 是否解析为 Windows 标准格式
     * @return string
     */
    public static function resolve(string $path, bool $windows = false): string
    {
        $path = storage_path("app/$path");
        if ($windows) {
            $path = str_replace("/", "\\", $path);
        }
        return $path;
    }
}
