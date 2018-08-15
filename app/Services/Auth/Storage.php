<?php

namespace App\Services\Auth;

use App\Services\Auth\Contracts\StorageContract;

class Storage implements StorageContract
{

    /**
     * @var array
     */
    protected $tags;

    public function __construct(array $tags)
    {
        $this->tags = $tags;
    }

    public function add($key, $value, $minutes)
    {
        app('cache')->tags($this->tags)->put($key, $value, $minutes);
    }

    public function forever($key, $value)
    {
        app('cache')->tags($this->tags)->forever($key, $value);
    }

    public function get($key)
    {
        return app('cache')->tags($this->tags)->get($key);
    }

    public function destroy($key)
    {
        app('cache')->tags($this->tags)->forget($key);
    }

    public function flush()
    {
        app('cache')->tags($this->tags)->flush();
    }
}
