<?php

use Illuminate\Database\Seeder;

class ApplicationRoleSeeder extends Seeder
{

    use \Database\seeds\SeederTrait;

    public function run()
    {
        $fields = ['application_id', 'role_id', 'default'];
        $data = [

            // niceboat
            ['34b00a60-9dac-11e8-885f-cf7934a49f67', 'f9d69440-9f1a-11e8-a282-ed05523a925f', false], // root
            ['34b00a60-9dac-11e8-885f-cf7934a49f67', '0054f7b0-9f1b-11e8-adb5-ed6931b40c8e', false], // admin
            ['34b00a60-9dac-11e8-885f-cf7934a49f67', '06627190-9f1b-11e8-8527-0f0e8792cfcc', true], // customer
            ['34b00a60-9dac-11e8-885f-cf7934a49f67', '0c36d700-9f1b-11e8-862d-e71c4e23d106', false], // owner
            ['34b00a60-9dac-11e8-885f-cf7934a49f67', '1173e540-9f1b-11e8-a4e7-13f40fb68bc5', false], // captain

            // nicelimo
            ['9c623080-9dac-11e8-ae88-fb267a8a82d8', 'f9d69440-9f1a-11e8-a282-ed05523a925f', false], // root
            ['9c623080-9dac-11e8-ae88-fb267a8a82d8', '0054f7b0-9f1b-11e8-adb5-ed6931b40c8e', false], // admin
            ['9c623080-9dac-11e8-ae88-fb267a8a82d8', '06627190-9f1b-11e8-8527-0f0e8792cfcc', true], // customer
            ['9c623080-9dac-11e8-ae88-fb267a8a82d8', '0c36d700-9f1b-11e8-862d-e71c4e23d106', false], // owner
            ['9c623080-9dac-11e8-ae88-fb267a8a82d8', '16e2a250-9f1b-11e8-8b6e-0d50a48591e5', false], // driver

            // nicegym
            ['a1b8b980-d346-11e8-ab92-5bf2862614e0', 'f9d69440-9f1a-11e8-a282-ed05523a925f', false], // root
            ['a1b8b980-d346-11e8-ab92-5bf2862614e0', '0054f7b0-9f1b-11e8-adb5-ed6931b40c8e', false], // admin
            ['a1b8b980-d346-11e8-ab92-5bf2862614e0', '06627190-9f1b-11e8-8527-0f0e8792cfcc', true], // customer
            ['a1b8b980-d346-11e8-ab92-5bf2862614e0', 'cff3be20-d346-11e8-a478-4f6f6c3dab75', false], // professional
        ];

        $this->seedData('application_role', $fields, $data, ['application_id', 'role_id']);
    }
}
