<?php

use Illuminate\Database\Seeder;

class ApplicationsSeeder extends Seeder
{

    use \Database\seeds\SeederTrait;

    public function run()
    {
        $fields = ['id', 'app_name'];
        $data = [
            ['34b00a60-9dac-11e8-885f-cf7934a49f67', 'niceboat'],
            ['9c623080-9dac-11e8-ae88-fb267a8a82d8', 'nicelimo'],
            ['4fdf41b0-9fca-11e8-887d-f51069175afa', 'nicetaxi'],
        ];

        $this->seedData('applications', $fields, $data);
    }
}
