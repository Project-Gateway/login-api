<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $fillable = ['account_id', 'provider', 'social_id', 'avatar'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
