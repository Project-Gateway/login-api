<?php

class UsersSeeder extends \Illuminate\Database\Seeder
{
    use \Database\seeds\SeederTrait;

    public function run()
    {
        $fields = ['id', 'password', 'first_name', 'last_name', 'phone'];
        $data = [
            ['a0caa790-9db0-11e8-a8c0-f5011205b59b', app('hash')->make('secret'), 'Orion', 'Root', null],
            ['c002d250-9db0-11e8-b76e-35dd1b579388', app('hash')->make('secret'), 'Niceboat', 'Admin', null],
            ['d32bc120-9db0-11e8-b790-01f97c50ed91', app('hash')->make('secret'), 'Nicelimo', 'Admin', null],
            ['d33d5ec0-c004-11e8-a1db-2b5e8ba9cd1d', app('hash')->make('secret'), 'Jose', 'Guerrero', '713-941-2868'],
            ['e2744630-c004-11e8-abe0-45a3fb1168bb', app('hash')->make('secret'), 'Leo', 'Roberts', '570-268-5873'],
            ['e92f5be0-c004-11e8-abbc-67c31d0bf64b', app('hash')->make('secret'), 'David', 'Bowers', '262-733-1462'],
        ];

        $this->seedData('users', $fields, $data);
    }

}
