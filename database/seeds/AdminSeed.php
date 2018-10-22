<?php

use App\User;
use Illuminate\Database\Seeder;

class AdminSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name' => 'Alfred', 
            'last_name' => 'Moore',
            'slug' => 'alfred-moore',
            'email' => 'ak_moore@live.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
    }
}
