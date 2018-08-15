<?php

namespace App\Services\Auth\Contracts;

use App\Services\Jwt\Contracts\TokenContract;
use App\Services\Jwt\Contracts\TokenFactoryContract;

interface AuthManagerContract
{

    public function __construct(TokenFactoryContract $tokenFactory, WhitelistContract $whitelist);

    public function login(ApplicationContract $application, UserContract $user, string $role): array;

    public function logout();

    public function getToken(): ?TokenContract;

    public function setToken(TokenContract $token);

    public function check(TokenContract $token): bool;

    public function retrieveSocialLoginUrl($provider, $redirectUri = null): ?string;

    public function retrieveSocialUser($provider, $redirectUri = null);

}
