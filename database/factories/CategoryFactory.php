<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\category;
use Faker\Generator as Faker;

$factory->define(category::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
        'slug' => $faker->slug,
        'content' => $faker->content
    ];
});
