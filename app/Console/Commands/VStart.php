<?php

namespace App\Console\Commands;

use App\Helper\V2ray;
use App\Jobs\SubscribeUpdate;
use App\V2rayV\Setting;
use Illuminate\Console\Command;

class VStart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vvv:start {--port}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start V2rayV';

    /**
     * Execute the console command.
     *
     * @param Setting $setting
     * @param V2ray $v2ray
     * @return mixed
     */
    public function handle(Setting $setting, V2ray $v2ray)
    {
        $database = database_path('database.sqlite');
        if (!file_exists($database)) {
            copy("${database}.example", $database);
            $this->call('migrate', [
                '--force' => true
            ]);
        }
        $this->line('Ready');
        if ($setting->auto_update_v2ray || file_exists(storage_path('app/v2ray/v2ray.exe'))) {
            dispatch(static function () use ($v2ray) {
                $v2ray->checkUpdate();
            });
        }
        SubscribeUpdate::dispatch();
        event('V2rayControl');
        $this->call('serve', [
            '--port' => $this->input->getOption('port') ?: 8246
        ]);
    }
}
