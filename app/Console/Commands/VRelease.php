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
    protected $signature = 'vvv:release';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release V2rayV';

    private $base_path;

    private $ignorePath = [
        'app/Console/Commands/VRelease.php',
        '.git',
        '.idea',
        '.gitignore',
        'Launcher',
        'node_modules',
        'php',
        'resources/css',
        'resources/js',
        'resources/sass',
        'tests',
        '.babelrc.js',
        '.browserslistrc',
        '.editorconfig',
        '.env.example',
        '.env.vvv.dev',
        '.eslintignore',
        '.eslintrc.js',
        '.gitattributes',
        '.gitignore',
        '.styleci.yml',
        '_ide_helper.php',
        'composer.lock',
        'package.json',
        'phpunit.xml',
        'webpack.mix.js',
        'yarn.lock'
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->base_path = base_path();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $npm_script = [
            'install',
            'prod'
        ];
        foreach ($npm_script as $value) {
            $cmd = "yarn $value";
            $this->runCmd($cmd);
        }
        $vs_where = getenv('ProgramFiles(x86)') . '\\Microsoft Visual Studio\\Installer\\vswhere.exe';
        if (!file_exists($vs_where)) {
            $this->line("Not found: $vs_where", 'fg=red');
            exit();
        }
        $cmd = "\"${vs_where}\" -nologo -latest -property installationPath";
        $this->line("Run $cmd", 'fg=blue');
        $vsPath = exec($cmd);
        if (!file_exists($vsPath)) {
            $this->line('Not found Microsoft Visual Studio install path.', 'fg=red');
            exit();
        }
        $msBuild = "${vsPath}\\MSBuild\\Current\\Bin\\MSBuild.exe";
        if (!file_exists($msBuild)) {
            $this->line("No msbuild found in the Microsoft Visual Studio installation directory: $msBuild", 'fg=red');
            exit();
        }
        $property = "Configuration=Release;OutDir={$this->base_path};DebugSymbols=false;DebugType=None";
        $csproj_path = realpath("{$this->base_path}/Launcher/Launcher/Launcher.csproj");
        $cmd = "\"${msBuild}\" -t:Rebuild -p:$property $csproj_path";
        $this->runCmd($cmd);
        $zip = new ZipArchive();
        $zipPath = "{$this->base_path}/../V2rayV.zip";
        $zipPath = realpath($zipPath);
        touch($zipPath);
        $res = $zip->open($zipPath, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
        $this->line('Creating zip archive', 'fg=green');
        if ($res !== true) {
            $this->line("Zip open fail: $zipPath", 'fg=red');
            exit();
        }
        $this->addFilesToZip($zip, $this->base_path);
        $zip->close();
        $this->line("Zip save to: $zipPath", 'fg=green');
        sleep(1);
        exec("explorer.exe /select, \"${zipPath}\"");
    }

    private function runCmd(string $cmd): void
    {
        $this->line("Run $cmd", 'fg=blue');
        $proc = proc_open($cmd, [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ], $pipes, $this->base_path);
        if (!is_resource($proc)) {
            $this->line("Run fail: $cmd", 'fg=red');
            exit(-1);
        }
        while ($buffer = fgets($pipes[1])) {
            echo $buffer;
        }
        echo PHP_EOL;
        fclose($pipes[1]);
        proc_close($proc);
    }

    private function addFilesToZip(ZipArchive $zip, string $path): void
    {
        // 获取基于项目根目录的相对路径
        $name = str_replace(array($this->base_path, "\\"), array('', '/'), $path);
        if (!empty($name) && in_array(substr($name, 1), $this->ignorePath, true)) {
            return;
        }
        if (is_dir($path)) {
            // 不添加根目录
            if (!empty($name)) {
                $zip->addEmptyDir($name);
            }
            $files = array_diff(scandir($path), ['.', '..']);
            foreach ($files as $value) {
                $this->addFilesToZip($zip, "${path}/${value}");
            }
        } else {
            $zip->addFile($path, $name);
        }
    }
}
