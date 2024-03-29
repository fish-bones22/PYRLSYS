<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(\App\Models\EmployeeDetail::class, function (Faker $faker) {
    return [
        'key' => $faker->word,
        'value' => $faker->word,
        'detail' => $faker->sentence,
        'displayName' => $faker->word
    ];
});
