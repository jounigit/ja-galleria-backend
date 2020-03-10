<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'testi',
            'email' => 'testi@mail.com',
            'email_verified_at' => now(),
            'is_admin' => 0,
            'password' => bcrypt('password'),
        ]);
    }
}
