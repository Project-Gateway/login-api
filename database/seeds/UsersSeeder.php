<?php

class UsersSeeder extends \Illuminate\Database\Seeder
{
    use \Database\seeds\SeederTrait;

    public function run()
    {
        $fields = ['id', 'password', 'name'];
        $data = [
            ['a0caa790-9db0-11e8-a8c0-f5011205b59b', app('hash')->make('secret'), 'Orion Root'],
            ['c002d250-9db0-11e8-b76e-35dd1b579388', app('hash')->make('secret'), 'Niceboat Admin'],
            ['d32bc120-9db0-11e8-b790-01f97c50ed91', app('hash')->make('secret'), 'Nicelimo Admin'],
            ['2e3e74f0-9fca-11e8-8f58-cd8ec9b0809b', app('hash')->make('secret'), 'Nicetaxi Admin'],
        ];

        $this->seedData('users', $fields, $data);
    }

}
