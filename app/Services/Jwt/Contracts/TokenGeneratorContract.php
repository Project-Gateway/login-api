<?php

namespace App\Services\Jwt\Contracts;

interface TokenGeneratorContract
{

    public function __construct(string $key);

    public function generate(array $headers, array $payload): string;
}
