<?php

namespace App\Services;

use App\Models\SocialAccount;
use Illuminate\Support\Collection;
use Laravel\Socialite\Contracts\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;

class AuthService
{

    public function socialLogin(string $provider, User $user)
    {

    }

    public function retrieveLoginResponse($token)
    {
        /** @var User $user */
        $user = app('auth')->user();

        return [
            'accessToken' => $token,
            'tokenType' => 'bearer',
            'expiresIn' => app('auth')->factory()->getTTL() * 60,
            'emails' => $user->emails->map(function($item) {
                return $item->email;
            }),
        ];
    }

    public function retrieveSocialLoginUrl($provider, $redirectUri = null)
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
