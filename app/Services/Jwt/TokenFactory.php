<?php

namespace App\Services\Jwt;

use App\Services\Jwt\Contracts\TokenContract;
use App\Services\Jwt\Contracts\TokenFactoryContract;
use Webpatser\Uuid\Uuid;

class TokenFactory implements TokenFactoryContract
{
    public function build($aud, $sub, array $customClaims): TokenContract
    {

        $headers = ['alg' => 'HS256', 'typ' => 'JWT'];

        $iss = 'orion.com';
        $now = time();
        $exp = 24 * 60 * 60;

        $payload = [
            'iss' => $iss,
            'jti' => Uuid::generate()->string,
            'aud' => $aud,
            'sub' => $sub,
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + $exp,
        ];

        $payload += $customClaims;

        return new Token($headers, $payload, new TokenGenerator('mysecretkey'));

    }

    public function parse(string $token): ?TokenContract
    {
        if (!([$headers, $payload] = (new TokenParser('mysecretkey'))->parse($token))) {
            return null;
        }
        return new Token($headers, $payload, new TokenGenerator('mysecretkey'));


    }
}
