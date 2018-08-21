<?php

use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{

    use \Database\seeds\SeederTrait;

    public function run()
    {
        $fields = ['id', 'role', 'parent_role_id', 'can_create_users'];
        $data = [

            ['f9d69440-9f1a-11e8-a282-ed05523a925f', 'root', null, true],
            ['0054f7b0-9f1b-11e8-adb5-ed6931b40c8e', 'admin', 'f9d69440-9f1a-11e8-a282-ed05523a925f', true],
            ['06627190-9f1b-11e8-8527-0f0e8792cfcc', 'customer', '0054f7b0-9f1b-11e8-adb5-ed6931b40c8e', false],
            ['0c36d700-9f1b-11e8-862d-e71c4e23d106', 'owner', '0054f7b0-9f1b-11e8-adb5-ed6931b40c8e', false],
            ['1173e540-9f1b-11e8-a4e7-13f40fb68bc5', 'captain', '0054f7b0-9f1b-11e8-adb5-ed6931b40c8e', false],
            ['16e2a250-9f1b-11e8-8b6e-0d50a48591e5', 'driver', '0054f7b0-9f1b-11e8-adb5-ed6931b40c8e', false],
        ];

        $this->seedData('roles', $fields, $data);
    }
}
