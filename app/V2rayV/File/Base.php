<?php

namespace App\V2rayV\File;

use App\Helper\Path;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

abstract class Base
{
    protected $path;

    /**
     * @param bool $real
     * @return string
     */
    public function getPath(bool $real = false): string
    {
        return Path::resolve($this->path, $real);
    }

    /**
     * @return string
     */
    public function readFile(): string
    {
        try {
            return Storage::get($this->path);
        } catch (FileNotFoundException $e) {
            return '';
        }
    }

    /**
     * @param string $data
     * @return bool
     */
    public function writeFile(string $data): bool
    {
        return Storage::put($this->path, $data);
    }

}
