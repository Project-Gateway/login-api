<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserEmail extends Model
{

    use UuidTrait;

    public $incrementing = false;

    protected $fillable = ['user_id', 'email'];

    protected $hidden = ['id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
