<?php

use App\User;
use App\Picture;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         factory(User::class, 5)->create()->each(function ($user) {
            $user->pictures()->save(factory(Picture::class)->make());
        }); /* */
        // factory(User::class, 5)->create();
    }
}
