<?php

use Illuminate\Database\Seeder;

class UserEmailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app('db')->table('user_emails')->insert([
            'id' => 1,
            'email' => 'jefersonparanaense@gmail.com',
            'user_id' => 1,
        ]);
    }
}
