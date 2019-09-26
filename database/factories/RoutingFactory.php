<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Routing;
use Faker\Generator as Faker;

$factory->define(Routing::class, static function (Faker $faker) {
    $proxy_list = [];
    for ($i = 0, $iMax = random_int(1, 10); $i < $iMax; $i++) {
        if (random_int(1, 2) === 1) {
            $proxy_list[] = $faker->domainName;
        } else {
            $proxy_list[] = $faker->ipv4;
        }
    }
    $direct_list = [];
    for ($i = 0, $iMax = random_int(1, 10); $i < $iMax; $i++) {
        if (random_int(1, 2) === 1) {
            $direct_list[] = $faker->domainName;
        } else {
            $direct_list[] = $faker->ipv4;
        }
    }
    $block_list = [];
    for ($i = 0, $iMax = random_int(1, 10); $i < $iMax; $i++) {
        if (random_int(1, 2) === 1) {
            $block_list[] = $faker->domainName;
        } else {
            $block_list[] = $faker->ipv4;
        }
    }
    $port = '';
    for ($i = 0; $i < 5; $i++) {
        if (random_int(1, 2) === 1) {
            $port .= random_int(1, 65535);
        } else {
            $port .= random_int(1, 25535) . '-' . random_int(25536, 65535);
        }
        $port .= ',';
    }
    return  [
        'name' => $faker->name,
        'proxy' => $proxy_list,
        'direct' => $direct_list,
        'block' => $block_list,
        'port' => $port,
        'network' => 'tcp,udp',
        'protocol' => [],
        'servers' => [
            'all'
        ]
    ];
});
