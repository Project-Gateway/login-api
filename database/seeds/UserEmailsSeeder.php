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
            ['3db8c670-d34b-11e8-a235-cd3b26731182', 'admin@nicegym.com', 'e1ca6aa0-d34a-11e8-ad10-2b10b3b52bc9'],
            ['57950e10-d34c-11e8-90f1-2b145788a6b5', 'personal@gmail.com', '6adaf9d0-d34c-11e8-8aa8-dbec492e6487'],
            ['89b15f00-d34c-11e8-99fe-cbf8a1881882', 'customer@gmail.com', '8fcd37b0-d34c-11e8-9138-675e8b407715'],
            ['9121d2e0-c005-11e8-9e76-7b28946d7ec6', 'al2013@hotmail.com', 'd33d5ec0-c004-11e8-a1db-2b5e8ba9cd1d'],
            ['921f6430-c005-11e8-b9b9-dd6b5d7d2d72', 'sanford.jacobs@yahoo.com', 'e2744630-c004-11e8-abe0-45a3fb1168bb'],
            ['932f6320-c005-11e8-82fa-590c500c314b', 'delbert.cruicksha@yahoo.com', 'e92f5be0-c004-11e8-abbc-67c31d0bf64b'],
        ];

        $this->seedData('user_emails', $fields, $data);
    }
}
