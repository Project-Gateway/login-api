<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * @package App\Models
 * @property boolean $can_create_users
 * @property Collection $children
 * @property Collection $applications
 */
class Role extends Model
{
    use UuidTrait;

    public $incrementing = false;

    public static function findByRoleName(string $role): ?self
    {
        return static::where(['role' => $role])->first();
    }

    public function getChildrenAttribute()
    {
        return static::getChildren($this->id);
    }

    public function applications()
    {
        return $this->belongsToMany(Application::class);
    }

    protected static function getChildren($id): Collection
    {
        /** @var Collection $result */
        $result = static::where(['parent_role_id' => $id])->get();

        foreach ($result as $item) {
            $result = $result->merge(static::getChildren($item->id));
        }

        return $result;

    }
}
