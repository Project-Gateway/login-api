<?php

namespace App\Services\Auth;

use App\Services\Auth\Contracts\ApplicationContract;
use App\Services\Auth\Contracts\AuthManagerContract;
use App\Services\Auth\Contracts\UserContract;
use App\Services\Auth\Contracts\WhitelistContract;
use App\Services\Jwt\Contracts\TokenContract;
use App\Services\Jwt\Contracts\TokenFactoryContract;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;

class AuthManager implements AuthManagerContract
{
    /**
     * @var TokenFactoryContract
     */
    protected $tokenFactory;

    /**
     * @var WhitelistContract
     */
    protected $whiteList;

    /**
     * @var TokenContract
     */
    protected $token = null;

    public function __construct(TokenFactoryContract $tokenFactory, WhitelistContract $whitelist)
    {
        $this->tokenFactory = $tokenFactory;
        $this->whiteList = $whitelist;
    }

    public function login(ApplicationContract $application, UserContract $user, string $role): array
    {
        $emails = $user->getAllEmails();
        $role = $application->getName() . '_' . $role;
        $token = $this->tokenFactory->build($application->getName(), $user->getId(), [
            'emails' => $emails,
            'role' => $role
        ]);

        // check if the token is on the whitelist, if it is, use the cached one
        // this way we avoid to generate multiple tokens for the same user/application/role
        // if the token is not on the whitelist, include it
        if ($cached = $this->whiteList->get($token)) {
            $token = $cached;
        } else {
            $this->whiteList->add($token);
        }
        $this->token = $token;

        return [
            'accessToken' => (string) $token,
            'tokenType' => 'bearer',
            'expiresIn' => $token->getClaimValue('exp') - time(),
            'emails' => $emails,
        ];
    }

    public function getToken(): ?TokenContract
    {
        return $this->token;
    }

    public function setToken(TokenContract $token)
    {
        $this->token = $token;
    }

    public function check(TokenContract $token): bool
    {
        return $this->whiteList->has($token);
    }

    public function logout()
    {
        $this->whiteList->invalidate($this->token);
    }

    public function retrieveSocialLoginUrl($provider, $redirectUri = null): ?string
    {
        if (!config("services.$provider")) {
            return null;
        }

        /** @var AbstractProvider $socialite */
        $socialite = Socialite::driver($provider);

        if ($redirectUri) {
            $socialite->redirectUrl($redirectUri);
        }
        return $socialite->stateless()->redirect()->getTargetUrl();
    }

    public function retrieveSocialUser($provider, $redirectUri = null)
    {
        /** @var AbstractProvider $socialite */
        $socialite = Socialite::driver($provider);
        $socialite->redirectUrl($redirectUri);
        return Socialite::driver($provider)->stateless()->user();
    }

}
