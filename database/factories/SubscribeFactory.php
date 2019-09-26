<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Subscribe;
use Faker\Generator as Faker;

$factory->define(Subscribe::class, static function (Faker $faker) {
    return  [
        'name' => $faker->name,
        'url' => $faker->url,
        'mux' => [
            'enabled' => false,
        ]
    ];
});
