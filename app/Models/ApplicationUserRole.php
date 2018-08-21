<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class ApplicationUser
 * @package App\Models
 * @property string application_id
 * @property string user_id
 * @property string role_id
 * @property boolean default
 */
class ApplicationUserRole extends Pivot
{

    public $incrementing = false;

}
