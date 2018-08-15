<?php

namespace App\Models;


use App\Services\Auth\Contracts\ApplicationContract;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Application
 * @package App\Models
 * @property int $id
 * @property UserEmail[] $emails
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

    public static function findByName($name)
    {
        return static::where(['app_name' => $name])->first();
    }

    public function getName(): string
    {
        return $this->app_name;
    }
}
