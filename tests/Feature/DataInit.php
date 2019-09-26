<?php

namespace Tests\Feature;

use App\V2rayV\Server;
use App\Models\Server as ServerModel;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DataInit extends TestCase
{
    public function testData(): void
    {
        try {
            $server = $this->app->make(Server::class);
        } catch (BindingResolutionException $e) {
            exit();
        }
        for ($i = 0; $i < 100; $i++) {
            try {
                $id = $server->add(factory(ServerModel::class)->make()->toArray());
                $this->assertEquals($id !== 0, true);
            } catch (Exception $e) {
                $this->addWarning($e->getMessage());
            }
        }
    }
}
