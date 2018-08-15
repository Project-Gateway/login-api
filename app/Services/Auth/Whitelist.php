<?php

namespace App\Services\Auth;

use App\Services\Auth\Contracts\StorageContract;
use App\Services\Auth\Contracts\WhitelistContract;
use App\Services\Jwt\Contracts\TokenContract;

class Whitelist implements WhitelistContract
{

    private $keyClaims = ['iss', 'aud', 'sub', 'role'];

    /**
     * @var StorageContract
     */
    protected $storage;

    public function __construct(StorageContract $storage)
    {
        $this->storage = $storage;
    }

    protected function hash(array $payload): string
    {
        return md5(json_encode(array_intersect_key($payload, array_flip($this->keyClaims))));
    }

    public function add(TokenContract $token)
    {
        $key = $this->hash($token->getPayload());
        $minutes = (int) ((($token->getPayload()['exp'] ?? (24 * 60 * 60)) - time()) / 60);
        $this->storage->add($key, $token, $minutes);
    }

    public function get(TokenContract $token)
    {
        $key = $this->hash($token->getPayload());
        return $this->storage->get($key);
    }

    public function has(TokenContract $token): bool
    {
        return (bool) $this->get($token);
    }

    public function invalidate(TokenContract $token)
    {
        $key = $this->hash($token->getPayload());
        $this->storage->destroy($key);
    }

    public function flush()
    {
        $this->storage->flush();
    }
}
