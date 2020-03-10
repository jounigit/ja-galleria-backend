<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuperUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'superuser',
            'email' => env('SUPERUSER_EMAIL'),
            'email_verified_at' => now(),
            'is_admin' => 1,
            'password' => bcrypt(env('SUPERUSER_PASSWORD')),
        ]);
    }
}
