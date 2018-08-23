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

    public function setToken(TokenContract $token): void;

    public function getApplication(): string;

    public function setApplication(string $application): void;

    public function check(TokenContract $token): bool;

    public function retrieveSocialLoginUrl($provider, $redirectUri = null): ?string;

    public function retrieveSocialUser($provider, $redirectUri = null);

    public function registerUser(string $email, string $password = null, string $role = null, $socialProvider = null, $socialId = null, $avatar = null): ?UserContract;

    public function getUserId(): string;

    public function getRole(): string;

    public function getChildRoles(): array;

}
