<?php

namespace App\Models;


use App\Services\Auth\Contracts\UserContract;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * @package App\Models
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
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

    public function scopeByApplication($query, $application)
    {
        return $query
            ->join('application_user', ['application_user.user_id' => 'users.id'])
            ->join('applications', ['application_user.application_id' => 'applications.id'])
            ->where('applications.app_name', $application);
    }

    public function scopeByRole($query, $role, $application = null)
    {
        $query
            ->join('application_user', ['application_user.user_id' => 'users.id'])
            ->join('applications', ['application_user.application_id' => 'applications.id'])
            ->join('application_user_role', [
                'application_user_role.application_id' => 'application_user.application_id',
                'application_user_role.user_id' => 'application_user.user_id'
            ])
            ->join('roles', ['roles.id' => 'application_user_role.role_id'])
            ->where('roles.role', $role)
            ->select('users.*');

        if ($application !== null) {
            $query->where('applications.app_name', $application);
        }

        return $query;
    }
}
