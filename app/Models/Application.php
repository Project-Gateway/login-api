<?php

namespace App\Models;


use App\Services\Auth\Contracts\ApplicationContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Application
 * @package App\Models
 * @property int $id
 * @property UserEmail[] $emails
 * @property Collection $roles
 */
class Application extends Model implements ApplicationContract
{
    use UuidTrait;

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'app_name',
    ];

    /**
     * Scope a query to only include the applications of one name
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByName($query, $name)
    {
        return $query->where(['app_name' => $name]);
    }

    public function getName(): string
    {
        return $this->app_name;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function defaultRole()
    {
        return $this->roles()->where(['default' => true]);
    }

}
