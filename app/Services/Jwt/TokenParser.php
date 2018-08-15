<?php

namespace App\Services\Jwt;

use App\Services\Jwt\Contracts\TokenParserContract;

class TokenParser implements TokenParserContract
{

    /**
     * @var string
     */
    protected $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * @param string $token
     * @return array[]
     */
    public function parse(string $token): ?array
    {

        // verify token format
        if (!preg_match('/^[a-zA-Z0-9\-_]+?\.[a-zA-Z0-9\-_]+?\.([a-zA-Z0-9\-_]+)?$/', $token)) {
            return null;
        }

        // split the token
        [$headersEncoded, $payloadEncoded, $signatureEncoded] = explode('.', trim($token));

        // verify the signature
        $expected = hash_hmac('SHA256', "$headersEncoded.$payloadEncoded", $this->key, true);
        $signature = $this->base64urlDecode($signatureEncoded);
        if (!hash_equals($signature, $expected)) {
            return null;
        }

        // decode the jsons into arrays
        $headers = $this->base64urlDecode($headersEncoded);
        $payload = $this->base64urlDecode($payloadEncoded);

        // return
        return [
            json_decode($headers, true),
            json_decode($payload, true)
        ];

    }

    protected function base64urlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
