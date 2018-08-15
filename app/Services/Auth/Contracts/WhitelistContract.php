<?php

namespace App\Services\Auth\Contracts;

use App\Services\Jwt\Contracts\TokenContract;

interface WhitelistContract
{
    public function add(TokenContract $token);

    public function get(TokenContract $token);

    public function has(TokenContract $token): bool;

    public function invalidate(TokenContract $token);

    public function flush();
}
