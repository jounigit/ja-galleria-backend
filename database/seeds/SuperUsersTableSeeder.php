<?php

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
            'email' => 'super@mail.com',
            'email_verified_at' => now(),
            'is_admin' => 1,
            'password' => bcrypt('suPerpass'),
        ]);
    }
}
