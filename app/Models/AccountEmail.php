<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountEmail extends Model
{
    protected $fillable = ['account_id', 'email'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
