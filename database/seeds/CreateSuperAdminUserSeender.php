<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CreateSuperAdminUserSeender extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement("truncate table users");
        \DB::statement("ALTER TABLE users AUTO_INCREMENT = 1");
        \DB::table('users')->insert([
            "id" => 1,
            'email_verified_at' => now(),
            'name' => "Super Admin",
            'email' => 'iamadmin@gmail.com',
            'password' => Hash::make('password'),
        ]);
    }
}
