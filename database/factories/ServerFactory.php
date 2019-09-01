<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Server;
use Faker\Generator as Faker;

$factory->define(Server::class, function (Faker $faker) {
    return [
        "name" => $faker->name,
        "address" => $faker->domainName,
        "port" => rand(1, 65535),
        "local_port" => rand(1, 65535),
        "protocol" => "vmess",
        "protocol_setting" => [
            "id" => $faker->uuid,
            "alterId" => 64,
            "security" => "auto"
        ],
        "network" => "tcp",
        "network_setting" => [
            "header" => [
                "type" => "none"
            ]
        ],
        "security" => "none",
        "security_setting" => [],
        "mux" => [
            "enabled" => false,
        ],
        "enable" => true
    ];
});

$factory->state(Server::class, "create", function (Faker $faker) {
    return [
        "protocol_setting" => json_encode([
            "id" => $faker->uuid,
            "alterId" => 64,
            "security" => "auto"
        ]),
        "network_setting" => json_encode([
            "header" => [
                "type" => "none"
            ]
        ]),
        "security_setting" => json_encode(new stdClass()),
        "mux" => json_encode([
            "enabled" => false,
        ]),
        "enable" => true
    ];
});
