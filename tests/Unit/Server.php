<?php

namespace Tests\Unit;

use App\Models\Server as ServerModel;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Server extends TestCase
{
    public function testStore(): void
    {
        $data = factory(ServerModel::class)->make()->toArray();
        $response = $this->postJson('/api/server/', $data);
        $response->assertOk();
        $response->assertJson([
            'success' => true
        ]);
        $this->assertDatabaseHas('servers', array_map(static function ($value) {
            if (is_array($value)) {
                return json_encode($value);
            }
            return $value;
        }, $data));
    }

    public function testUpdate(): void
    {
        $data = factory(ServerModel::class)->make()->toArray();
        $response = $this->putJson('/api/server/' . ServerModel::pluck('id')->random(), $data);
        $response->assertOk();
        $response->assertJson([
            'success' => true
        ]);
        $this->assertDatabaseHas('servers', array_map(static function ($value) {
            if (is_array($value)) {
                return json_encode($value);
            }
            return $value;
        }, $data));
    }

    public function testDelete(): void
    {
        $response = $this->delete('/api/server/' . ServerModel::pluck('id')->random());
        $response->assertOk();
        $response->assertJson([
            'success' => true
        ]);
        $this->assertDatabaseMissing('servers', [
            'id' => $response->json()['data']['id']
        ]);
    }

    public function testFilter(): void
    {
        $response = $this->get('/api/server-all?filter=enable');
        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);
        $json = json_decode($response->content(), true);
        foreach ($json['data'] as $item) {
            $this->assertEquals($item['enable'], true);
        }
    }
}
