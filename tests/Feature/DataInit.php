<?php

namespace Tests\Feature;

use App\Exceptions\V2ray\DataSaveFail;
use App\Exceptions\V2ray\ValidationException;
use App\V2rayV\Server;
use App\Models\Server as ServerModel;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DataInit extends TestCase
{
    public function testData()
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
            } catch (DataSaveFail $e) {
            } catch (ValidationException $e) {
            }
        }
    }
}
