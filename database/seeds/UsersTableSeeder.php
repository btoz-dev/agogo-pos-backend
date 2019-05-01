<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'esa',
            'email' => 'esa@mail.com',
            'password' => bcrypt('esa123'),
            'status' => true,
            'api_token' => str_random(100)
        ]);
    }
}
