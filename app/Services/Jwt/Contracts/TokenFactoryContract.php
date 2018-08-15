<?php

namespace App\Services\Jwt\Contracts;

interface TokenFactoryContract
{
    public function build($aud, $sub, array $customClaims): TokenContract;
    public function parse(string $token): ?TokenContract;
}
