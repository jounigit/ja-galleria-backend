<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Picture;
use Faker\Generator as Faker;

$factory->define(Picture::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
        'slug' => $faker->slug,
        'content' => $faker->content,
        'file' => $faker->file
    ];
});
