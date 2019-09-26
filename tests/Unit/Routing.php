<?php

namespace Tests\Unit;

use App\Models\Routing as RoutingModel;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Routing extends TestCase
{
    public function testStore(): void
    {
        $data = factory(RoutingModel::class)->make()->toArray();
        $response = $this->postJson('/api/routing/', $data);
        $response->assertOk();
        $response->assertJson([
            'success' => true
        ]);
        $this->assertDatabaseHas('routing', array_map(static function ($value) {
            if (is_array($value)) {
                return json_encode($value);
            }
            return $value;
        }, $data));
    }

    public function testUpdate(): void
    {
        $data = factory(RoutingModel::class)->make()->toArray();
        $response = $this->putJson('/api/routing/' . RoutingModel::pluck('id')->random(), $data);
        $response->assertOk();
        $response->assertJson([
            'success' => true
        ]);
        $this->assertDatabaseHas('routing', array_map(static function ($value) {
            if (is_array($value)) {
                return json_encode($value);
            }
            return $value;
        }, $data));
    }

    public function testUpdateDefault(): void
    {
        $data = factory(RoutingModel::class)->make()->toArray();
        $response = $this->putJson('/api/routing/default', $data);
        $response->assertOk();
        $response->assertJson([
            'success' => true
        ]);
    }

    public function testDelete(): void
    {
        $response = $this->delete('/api/routing/' . RoutingModel::pluck('id')->random());
        $response->assertOk();
        $response->assertJson([
            'success' => true
        ]);
        $this->assertDatabaseMissing('routing', [
            'id' => $response->json()['data']['id']
        ]);
    }
}
