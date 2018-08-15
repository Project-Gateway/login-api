<?php

class UserEmailsSeeder extends \Illuminate\Database\Seeder
{

    use \Database\seeds\SeederTrait;

    public function run()
    {
        $fields = ['id', 'email', 'user_id'];
        $data = [
            ['96f47400-9db0-11e8-93f7-4b2b016159d0', 'root@orion.com', 'a0caa790-9db0-11e8-a8c0-f5011205b59b'],
            ['11d22a00-9db1-11e8-89ad-d3f56c9b6e6c', 'admin@niceboat.com', 'c002d250-9db0-11e8-b76e-35dd1b579388'],
            ['18909050-9db1-11e8-bae0-5533f4af4bcc', 'admin@nicelimo.com', 'd32bc120-9db0-11e8-b790-01f97c50ed91'],
        ];

        $this->seedData('user_emails', $fields, $data);
    }
}
