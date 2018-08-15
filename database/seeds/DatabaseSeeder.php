<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersSeeder::class);
        $this->call(UserEmailsSeeder::class);
        $this->call(ApplicationsSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(ApplicationRoleSeeder::class);
        $this->call(ApplicationUserSeeder::class);
        $this->call(ApplicationUserRoleSeeder::class);
    }
}
