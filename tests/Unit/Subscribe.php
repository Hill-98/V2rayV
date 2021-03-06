<?php

namespace Tests\Unit;

use App\Models\Subscribe as SubscribeModel;
use Faker\Factory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Subscribe extends TestCase
{
    public function testStore(): void
    {
        $data = factory(SubscribeModel::class)->make()->toArray();
        $response = $this->postJson('/api/subscribe/', $data);
        $response->assertOk();
        $response->assertJson([
            'success' => true
        ]);
        $this->assertDatabaseHas('subscribe', array_map(static function ($value) {
            if (is_array($value)) {
                return json_encode($value);
            }
            return $value;
        }, $data));
    }

    public function testUpdate(): void
    {
        $data = factory(SubscribeModel::class)->make()->toArray();
        $response = $this->putJson('/api/subscribe/' . SubscribeModel::pluck('id')->random(), $data);
        $response->assertOk();
        $response->assertJson([
            'success' => true
        ]);
        $this->assertDatabaseHas('subscribe', array_map(static function ($value) {
            if (is_array($value)) {
                return json_encode($value);
            }
            return $value;
        }, $data));
    }

    public function testDelete(): void
    {
        $response = $this->delete('/api/subscribe/' . SubscribeModel::pluck('id')->random());
        $response->assertJson([
            'success' => true
        ]);
        $this->assertDatabaseMissing('subscribe', [
            'id' => $response->json()['data']['id']
        ]);
    }
}
