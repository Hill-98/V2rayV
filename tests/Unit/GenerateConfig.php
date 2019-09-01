<?php

namespace Tests\Unit;

use App\V2rayV\Config\Generate;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenerateConfig extends TestCase
{
    public function testExample()
    {
        $config = $this->app->make(Generate::class)();
        $this->assertEquals(is_array($config), true);
        print_r($config);
    }
}
