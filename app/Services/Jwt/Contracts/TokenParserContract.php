<?php

namespace App\Services\Jwt\Contracts;

interface TokenParserContract
{
    public function parse(string $token): ?array;
}
