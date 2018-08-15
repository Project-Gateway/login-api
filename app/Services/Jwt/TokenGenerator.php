<?php

namespace App\Services\Jwt;

use App\Services\Jwt\Contracts\TokenGeneratorContract;

class TokenGenerator implements TokenGeneratorContract
{

    /**
     * @var string
     */
    protected $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function generate(array $headers, array $payload): string
    {
        // build the headers
        $headersEncoded = $this->base64urlEncode(json_encode($headers));

        // build the payload
        $payloadEncoded = $this->base64urlEncode(json_encode($payload));

        // build the signature
        $signature = hash_hmac('SHA256', "$headersEncoded.$payloadEncoded", $this->key, true);
        $signatureEncoded = $this->base64urlEncode($signature);

        // build and return the token
        return "$headersEncoded.$payloadEncoded.$signatureEncoded";
    }

    protected function base64urlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
