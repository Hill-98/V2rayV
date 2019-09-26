<?php

namespace Tests\Unit;

use App\V2rayV\Config\Generate;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenerateConfig extends TestCase
{
    public function testExample(): void
    {
        try {
            $config = $this->app->make(Generate::class)();
            $this->assertEquals(is_array($config), true);
            Storage::put('config.test.json', json_encode($config, JSON_PRETTY_PRINT));
        } catch (BindingResolutionException $e) {
        }
    }
}
