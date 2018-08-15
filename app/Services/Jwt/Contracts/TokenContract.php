<?php

namespace App\Services\Jwt\Contracts;

interface TokenContract
{
    public function __construct(array $headers, array $payload, TokenGeneratorContract $tokenGenerator);
    public function getPayload(): array;
    public function getClaimValue(string $claim);
    public function __toString(): string;
}
