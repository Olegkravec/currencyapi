<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CreateSuperAdminUserSeender::class);
        $this->call(CreateBaseRolesSeeder::class);
        $this->call(CreateDefaultUsersSeeder::class);
        $this->call(FirstChatSeeder::class);
        $this->call(AllowedCurrencySeeder::class);
    }
}
