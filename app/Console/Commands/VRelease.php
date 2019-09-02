<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;

class VRelease extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "vvv:release";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Release V2rayV";

    private $ignorePath = [
        "app/Console/Commands/VRelease.php",
        ".git",
        ".idea",
        ".gitignore",
        "Launcher",
        "node_modules",
        "php",
        "resources/css",
        "resources/js",
        "resources/sass",
        "tests",
        ".babelrc.js",
        ".browserslistrc",
        ".editorconfig",
        ".env.example",
        ".env.vvv.dev",
        ".eslintignore",
        ".eslintrc.js",
        ".gitattributes",
        ".gitignore",
        ".styleci.yml",
        "_ide_helper.php",
        "composer.json",
        "composer.lock",
        "package.json",
        "phpunit.xml",
        "webpack.mix.js",
        "yarn.lock"
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $zip = new ZipArchive();
        $res = $zip->open(base_path() . "/../V2rayV.zip", ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
        if ($res !== true) {
            exit(-1);
        }
        $this->addFilesToZip($zip, base_path());
        $zip->close();
    }

    private function addFilesToZip(ZipArchive $zip, string $path)
    {
        $name = str_replace(base_path(), "", $path);
        $name = str_replace("\\", "/", $name);
        if (!empty($name) && in_array(substr($name, 1), $this->ignorePath)) {
            return;
        }
        if (is_dir($path)) {
            if (!empty($name)) {
                $zip->addEmptyDir($name);
            }
            $files = array_diff(scandir($path), [".", ".."]);
            foreach ($files as $value) {
                $this->addFilesToZip($zip, "${path}/${value}");
            }
        } else {
            $zip->addFile($path, $name);
        }
    }
}
