<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Users\Users::class, function (Faker $faker) {
    return [
        'user_name' => $faker->name,
        'email' => $faker->email,
        'password' => '123451234512345123451234512345'
    ];
});
