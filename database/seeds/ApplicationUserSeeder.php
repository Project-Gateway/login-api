<?php

use Illuminate\Database\Seeder;

class ApplicationUserSeeder extends Seeder
{

    use \Database\seeds\SeederTrait;

    public function run()
    {
        $fields = ['application_id', 'user_id'];
        $data = [

            ['34b00a60-9dac-11e8-885f-cf7934a49f67', 'a0caa790-9db0-11e8-a8c0-f5011205b59b'], // niceboot root
            ['9c623080-9dac-11e8-ae88-fb267a8a82d8', 'a0caa790-9db0-11e8-a8c0-f5011205b59b'], // nicelimo root
            ['34b00a60-9dac-11e8-885f-cf7934a49f67', 'c002d250-9db0-11e8-b76e-35dd1b579388'], // niceboat admin
            ['9c623080-9dac-11e8-ae88-fb267a8a82d8', 'd32bc120-9db0-11e8-b790-01f97c50ed91'], // nicelimo admin
            ['a1b8b980-d346-11e8-ab92-5bf2862614e0', 'e1ca6aa0-d34a-11e8-ad10-2b10b3b52bc9'], // nicegym admin
            ['a1b8b980-d346-11e8-ab92-5bf2862614e0', '6adaf9d0-d34c-11e8-8aa8-dbec492e6487'], // nicegym professional
            ['a1b8b980-d346-11e8-ab92-5bf2862614e0', '8fcd37b0-d34c-11e8-9138-675e8b407715'], // nicegym customer
        ];

        $this->seedData('application_user', $fields, $data, ['application_id', 'user_id']);
    }
}
