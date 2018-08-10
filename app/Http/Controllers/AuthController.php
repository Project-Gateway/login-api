<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use App\Models\Account;
use App\Models\AccountEmail;
use App\Services\AuthService;
use Illuminate\Http\Request;
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
    public function login(Request $request)
    {

        $message = 'Invalid username or password';

        // email exists?
        if (!($email = AccountEmail::where(['email' => $request->get('email')])->with('account')->first())) {
            return response(['message' => $message], 401);
        }

        // password is correct?
        if (empty($email->account->password) || !app('hash')->check($request->get('password'), $email->account->password)) {
            return response(['message' => $message], 401);
        }

        if (!$token = app('auth')->login($email->account)) {
            return response(['message' => $message], 401);
        }

        return response($this->authService->retrieveLoginResponse($token));
    }

    public function validateToken(\Illuminate\Http\Request $request)
    {
        return app('auth')->guard()->getPayload();
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
        app('auth')->logout();

        return response(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return response($this->authService->retrieveLoginResponse(app('auth')->refresh()));
    }


    public function providerUrl($provider, $redirectUri = null)
    {
        return response($this->authService->retrieveSocialLoginUrl($provider, $redirectUri));
    }

    public function providerUrls()
    {
        $response = [];
        foreach (app('request')->query('providers') as $providerJson) {
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
        $socialiteUser = $this->authService->retrieveSocialUser($provider, app('request')->query('redirectUri'));

        // user already registered with this social account
        if ($socialAccount = SocialAccount::where(['social_id' => $socialiteUser->getId()])->with('account')->first()) {
            return response($this->authService->retrieveLoginResponse(app('auth')->login($socialAccount->account)));
        }

        // user not registered, but email exists
        if (AccountEmail::where(['email' => $socialiteUser->getEmail()])->count()) {
            return response(['message' => "Email {$socialiteUser->getEmail()} already used by another account."], 401);
        }

        app('db')->beginTransaction();
        try {

            $user = new Account();
            $user->save();
            $userEmail = new AccountEmail();
            $userEmail->fill([
                'account_id' => $user->id,
                'email' => $socialiteUser->getEmail()
            ]);
            $userEmail->save();
            $socialAccount = new SocialAccount();
            $socialAccount->fill([
                'account_id' => $user->id,
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

        return response($this->authService->retrieveLoginResponse(app('auth')->login($user)));

    }
}
