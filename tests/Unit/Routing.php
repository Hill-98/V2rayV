<?php

namespace Tests\Unit;

use App\Models\Routing as RoutingModel;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Routing extends TestCase
{
    public function testStore()
    {
        $data = factory(RoutingModel::class)->make()->toArray();
        $response = $this->postJson("/api/routing/", $data);
        $response->assertOk();
        $response->assertJson([
            "success" => true
        ]);
        $this->assertDatabaseHas("routing", array_map(function ($value) {
            if (is_array($value)) {
                return json_encode($value);
            } else {
                return $value;
            }
        }, $data));
    }

    public function testUpdate()
    {
        $data = factory(RoutingModel::class)->make()->toArray();
        $response = $this->putJson("/api/routing/" . RoutingModel::pluck("id")->random(), $data);
        $response->assertOk();
        $response->assertJson([
            "success" => true
        ]);
        $this->assertDatabaseHas("routing", array_map(function ($value) {
            if (is_array($value)) {
                return json_encode($value);
            } else {
                return $value;
            }
        }, $data));
    }

    public function testUpdateDefault()
    {
        $response = $this->putJson("/api/routing/default", $this->genData());
        $response->assertOk();
        $response->assertJson([
            "success" => true
        ]);
    }

    public function testDelete()
    {
        $response = $this->delete("/api/routing/" . RoutingModel::pluck("id")->random());
        $response->assertOk();
        $response->assertJson([
            "success" => true
        ]);
        $this->assertDatabaseMissing("routing", [
            "id" => $response->json()["data"]["id"]
        ]);
    }
}
