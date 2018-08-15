<?php

namespace App\Services\Auth\Contracts;

interface StorageContract
{
    public function add($key, $value, $minutes);

    public function forever($key, $value);

    public function get($key);

    public function destroy($key);

    public function flush();
}
