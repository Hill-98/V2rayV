<?php

namespace App\Jobs;

use App\Helper\V2ray;
use App\V2rayV\Config\Generate;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class V2rayControl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    private $code;

    const systemdUnit = <<<EOF
[Unit]
Description=V2rayV Control V2ray
Requires=V2rayV.service
After=V2rayV.service
PartOf=V2rayV.service

[Service]
ExecStart=%s --config %s
EOF;


    /**
     * Create a new job instance.
     *
     * @param int $code
     */
    public function __construct(int $code = 0)
    {
        $this->code = $code;
    }

    /**
     * Execute the job.
     *
     * @param Generate $generate
     * @return void
     */
    public function handle(Generate $generate): void
    {
        if ($this->code !== 0) {
            $this->control($this->code);
            return;
        }
        $config = $generate();
        $this->control(V2ray::STOP);
        if (empty($config)) {
            return;
        }
        sleep(1);
        foreach ($config['log'] as $value) {
            if (file_exists($value)) {
                try {
                    unlink($value);
                } catch (\Exception $e) {
                }
            }
        }
        Storage::put('v2ray/config.json', json_encode($config, JSON_PRETTY_PRINT));
        $this->control(V2ray::START);
    }

    /**
     * @param $command
     * @return void
     */
    public function control($command): void
    {
        if (0 === stripos(PHP_OS_FAMILY, 'WIN')) {
            Storage::put('v2ray.vvv', $command);
            return;
        }
        $v2rayPath = system('/usr/bin/which v2ray');
        if (!file_exists($v2rayPath)) {
            $v2rayPath = '/usr/bin/v2ray/v2ray';
        }
        $userUnitPath = getenv('HOME').'/.config/systemd/user';
        $unitName = 'v2rayv-v2ray.service';
        if (!file_exists($userUnitPath) && !mkdir($userUnitPath, 755, true) && !is_dir($userUnitPath)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $userUnitPath));
        }
        file_put_contents("$userUnitPath/$unitName", sprintf(self::systemdUnit, $v2rayPath, Storage::path('v2ray/config.json')));
        exec('/usr/bin/systemctl --user daemon-reload');
        if ($command === V2ray::START) {
            exec("/usr/bin/systemctl --user start $unitName");
        } else {
            exec("/usr/bin/systemctl --user stop $unitName");
        }
    }
}
