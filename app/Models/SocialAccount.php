<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    use UuidTrait;

    public $incrementing = false;

    protected $fillable = ['user_id', 'provider', 'social_id', 'avatar'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
