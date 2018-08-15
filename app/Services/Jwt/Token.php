<?php

namespace App\Services\Jwt;

use App\Services\Jwt\Contracts\TokenContract;
use App\Services\Jwt\Contracts\TokenGeneratorContract;

class Token implements TokenContract
{

    /**
     * @var TokenGeneratorContract
     */
    protected $tokenGenerator;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    protected $payload = [];

    public function __construct(array $headers, array $payload, TokenGeneratorContract $tokenGenerator)
    {
        $this->headers = $headers;
        $this->payload = $payload;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getClaimValue(string $claim)
    {
        return $this->payload[$claim];
    }

    public function __toString(): string
    {
        return $this->tokenGenerator->generate($this->headers, $this->payload);
    }

}
