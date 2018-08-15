<?php

use Illuminate\Database\Seeder;

class ApplicationRoleSeeder extends Seeder
{

    use \Database\seeds\SeederTrait;

    public function run()
    {
        $fields = ['application_id', 'role_id'];
        $data = [

            // niceboat
            ['34b00a60-9dac-11e8-885f-cf7934a49f67', 'f9d69440-9f1a-11e8-a282-ed05523a925f'], // root
            ['34b00a60-9dac-11e8-885f-cf7934a49f67', '0054f7b0-9f1b-11e8-adb5-ed6931b40c8e'], // admin
            ['34b00a60-9dac-11e8-885f-cf7934a49f67', '06627190-9f1b-11e8-8527-0f0e8792cfcc'], // customer
            ['34b00a60-9dac-11e8-885f-cf7934a49f67', '0c36d700-9f1b-11e8-862d-e71c4e23d106'], // owner
            ['34b00a60-9dac-11e8-885f-cf7934a49f67', '1173e540-9f1b-11e8-a4e7-13f40fb68bc5'], // captain

            // nicelimo
            ['34b00a60-9dac-11e8-885f-cf7934a49f67', 'f9d69440-9f1a-11e8-a282-ed05523a925f'], // root
            ['34b00a60-9dac-11e8-885f-cf7934a49f67', '0054f7b0-9f1b-11e8-adb5-ed6931b40c8e'], // admin
            ['34b00a60-9dac-11e8-885f-cf7934a49f67', '06627190-9f1b-11e8-8527-0f0e8792cfcc'], // customer
            ['34b00a60-9dac-11e8-885f-cf7934a49f67', '0c36d700-9f1b-11e8-862d-e71c4e23d106'], // owner
            ['34b00a60-9dac-11e8-885f-cf7934a49f67', '16e2a250-9f1b-11e8-8b6e-0d50a48591e5'], // driver
        ];

        $this->seedData('application_role', $fields, $data, ['application_id', 'role_id']);
    }
}
