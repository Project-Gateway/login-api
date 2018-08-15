<?php

use Illuminate\Database\Seeder;

class ApplicationUserRoleSeeder extends Seeder
{

    use \Database\seeds\SeederTrait;

    public function run()
    {
        $fields = ['application_id', 'user_id', 'role_id', 'default'];
        $data = [

            // niceboat
            ['34b00a60-9dac-11e8-885f-cf7934a49f67', 'a0caa790-9db0-11e8-a8c0-f5011205b59b', 'f9d69440-9f1a-11e8-a282-ed05523a925f', true], // root
            ['34b00a60-9dac-11e8-885f-cf7934a49f67', 'c002d250-9db0-11e8-b76e-35dd1b579388', '0054f7b0-9f1b-11e8-adb5-ed6931b40c8e', true], // admin

            // nicelimo
            ['9c623080-9dac-11e8-ae88-fb267a8a82d8', 'a0caa790-9db0-11e8-a8c0-f5011205b59b', 'f9d69440-9f1a-11e8-a282-ed05523a925f', true], // root
            ['9c623080-9dac-11e8-ae88-fb267a8a82d8', 'd32bc120-9db0-11e8-b790-01f97c50ed91', '0054f7b0-9f1b-11e8-adb5-ed6931b40c8e', true], // admin
            
        ];

        $this->seedData('application_user_role', $fields, $data, ['application_id', 'user_id', 'role_id']);
    }
}
