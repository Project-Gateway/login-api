<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use App\Models\User;
use App\Models\UserEmail;
use App\Services\AuthService;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class AuthController extends Controller
{

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $message = 'Invalid username or password';

        if (!($email = UserEmail::where(['email' => request('email')])->with('user')->first())) {
            return response(['message' => $message], 401);
        }

        if (empty($email->user->password) || !Hash::check(request('password'), $email->user->password)) {
            return response(['message' => $message], 401);
        }

        if (!$token = auth()->login($email->user)) {
            return response(['message' => $message], 401);
        }

        return response($this->authService->retrieveLoginResponse($token));
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return response($this->authService->retrieveLoginResponse(auth()->refresh()));
    }


    public function providerUrl($provider, $redirectUri = null)
    {
        return response($this->authService->retrieveSocialLoginUrl($provider, $redirectUri));
    }

    public function providerUrls()
    {
        $response = [];
        foreach (request()->query('providers') as $providerJson) {
            $providerInfo = json_decode($providerJson);
            $response[$providerInfo->provider] = $this->authService->retrieveSocialLoginUrl($providerInfo->provider, $providerInfo->redirectUri);
        }
        return response($response);
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function providerCallback($provider)
    {

        /** @var SocialiteUser $socialiteUser */
        $socialiteUser = $this->authService->retrieveSocialUser($provider, request()->query('redirectUri'));

        // user already registered with this social account
        if ($socialAccount = SocialAccount::where(['social_id' => $socialiteUser->getId()])->with('user')->first()) {
            return response($this->authService->retrieveLoginResponse(auth()->login($socialAccount->user)));
        }

        // user not registered, but email exists
        if (UserEmail::where(['email' => $socialiteUser->getEmail()])->count()) {
            return response(['message' => "Email {$socialiteUser->getEmail()} already used by another account."], 401);
        }

        app('db')->beginTransaction();
        try {

            $user = new User();
            $user->save();
            $userEmail = new UserEmail();
            $userEmail->fill([
                'user_id' => $user->id,
                'email' => $socialiteUser->getEmail()
            ]);
            $userEmail->save();
            $socialAccount = new SocialAccount();
            $socialAccount->fill([
                'user_id' => $user->id,
                'provider' => $provider,
                'social_id' => $socialiteUser->getId(),
                'avatar' => $socialiteUser->getAvatar(),
            ]);
            $socialAccount->save();

        } catch (\Exception $e) {
            app('db')->rollBack();
            return response(['message' => 'Can\'t create the account'], 500);
        }
        app('db')->commit();

        return response($this->authService->retrieveLoginResponse(auth()->login($user)));

    }
}
