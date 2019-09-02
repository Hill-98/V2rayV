<?php

namespace App\V2rayV\File;

use App\Helper\Path;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

abstract class Base
{
    protected $path;

    public function getPath(bool $real = false): string
    {
        return Path::resolve($this->path, $real);
    }

    public function readFile(): string
    {
        try {
            return Storage::get($this->path);
        } catch (FileNotFoundException $e) {
            return "";
        }
    }

    public function writeFile(string $data): bool
    {
        return Storage::put($this->path, $data);
    }

}
