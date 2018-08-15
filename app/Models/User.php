<?php

namespace App\Models;


use App\Services\Auth\Contracts\UserContract;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * @package App\Models
 * @property int $id
 * @property UserEmail[] $emails
 */
class User extends Model implements UserContract
{

    use UuidTrait;

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function emails()
    {
        return $this->hasMany(UserEmail::class);
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function applications()
    {
        return $this->belongsToMany(Application::class)->using(ApplicationUser::class);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAllEmails()
    {
        return $this->emails->map(function($item) {
            return $item->email;
        });
    }
}
