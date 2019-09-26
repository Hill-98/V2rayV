<?php

namespace Tests\Feature;

use App\Models\Server;
use Exception;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShareURLTest extends TestCase
{
    public function testShareURL(): void
    {
        if (Server::count() < 5) {
            Server::insert(factory(Server::class, 5)->state('create')->make()->toArray());
        }
        $encrypt_method = 'aes-256-ctr';
        $data = Server::pluck('id')->random(5)->toArray();
        $response = $this->postJson('/api/server/share-url/export?encrypt=true', $data);
        $response->assertOk();
        $json = $response->json()['data'];
        $password = '';
        if ($json['password'] === '') {
            $password = $json['password'];
        }
        $urls = $json['vvv'];
        foreach ($urls as $value) {
            if ($password === '') {
                $d = base64_decode(Str::after($value, 'vvv://'));
            } else {
                $d = base64_decode($value);
                $encrypt_data = Str::before($d, '::');
                $iv = Str::after($d, '::');
                $d = openssl_decrypt($encrypt_data, $encrypt_method, $password, OPENSSL_RAW_DATA, $iv);
            }
            $this->assertJson($d);
        }
        foreach ($data as $value) {
            try {
                Server::find($value)->delete();
            } catch (Exception $e) {
            }
        }
        $response = $this->postJson("/api/server/share-url/import?password=$password", $urls);
        $response->assertStatus(200);
        $this->assertCount($response->json()['data']['new'], $data);
    }
}
