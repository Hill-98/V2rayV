<?php
declare(strict_types=1);

namespace App\Helper;

class Path
{
    /**
     * @param string $path
     * @param bool $real
     * @return string
     */
    public static function resolve(string $path, bool $real = false): string
    {
        $path = storage_path("app/$path");
        if ($real) {
            $path = str_replace('/', "\\", $path);
        }
        return $path;
    }
}
