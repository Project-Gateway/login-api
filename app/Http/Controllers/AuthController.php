<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\SocialAccount;
use App\Models\User;
use App\Models\UserEmail;
use App\Services\Auth\Contracts\AuthManagerContract;
use Illuminate\Http\Request;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class AuthController extends Controller
{

    /**
     * @var AuthManagerContract
     */
    protected $authManager;

    public function __construct(AuthManagerContract $authManager)
    {
        $this->authManager = $authManager;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        // check if the application exists
        $application = Application::findByName($request->get('application'));
        if (!$application) {
            return response(['message' => 'Bad application name.'], 404);
        }

        $message = 'Invalid username or password';

        // email/user exists?
        $email = UserEmail::where(['email' => $request->get('email')])
            ->with(['user.applications' => function ($query) use ($application) {
                $query->where(['app_name' => $application->app_name]);
            }])
            ->first();
        if (!$email) {
            return response(['message' => $message], 401);
        }

        // password is correct?
        if (empty($email->user->password) || !app('hash')->check($request->get('password'), $email->user->password)) {
            return response(['message' => $message], 401);
        }

        // check the permissions and return
        return $this->checkUserPermissions($application, $email->user, $request->get('role') ?? null);

    }

    public function validateToken()
    {
        return $this->authManager->getToken()->getPayload();
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->authManager->logout();

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
        return response($this->authManager->retrieveSocialLoginUrl($provider, $redirectUri));
    }

    public function providerUrls()
    {
        $response = [];
        foreach (app('request')->query('providers') as $providerJson) {
            $providerInfo = json_decode($providerJson);
            $response[$providerInfo->provider] = $this->authManager->retrieveSocialLoginUrl($providerInfo->provider, $providerInfo->redirectUri);
        }
        return response($response);
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function providerCallback(Request $request, $provider)
    {
        // check if the application exists
        $application = Application::findByName($request->get('application'));
        if (!$application) {
            return response(['message' => 'Bad application name.'], 404);
        }

        /** @var SocialiteUser $socialiteUser */
        $socialiteUser = $this->authManager->retrieveSocialUser($provider, app('request')->query('redirectUri'));

        // check if the user is already registered with this social account
        $socialAccount = SocialAccount::where(['social_id' => $socialiteUser->getId()])
            ->with(['user.applications' => function ($query) use ($application) {
                $query->where(['app_name' => $application->app_name]);
            }])
            ->first();
        if ($socialAccount) {

            return $this->checkUserPermissions($application, $socialAccount->user, $request->get('role'));

        }

        // user not registered, but email exists. Respond with error.
        if (UserEmail::where(['email' => $socialiteUser->getEmail()])->count()) {
            return response(['message' => "Email {$socialiteUser->getEmail()} already used by another account."], 401);
        }

        // register new user
        app('db')->beginTransaction();
        try {

            $user = new User();
            $user->save();
            $userEmail = new UserEmail();
            $userEmail->fill([
                'user' => $user->id,
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

        return response($this->authManager->login($application, $user, 'user'));

    }

    protected function checkUserPermissions($application, $user, $role = null)
    {
        // Isn't the user linked to the application?
        if (!$user->applications->count()) {
            // TODO - Register the user with the application, as a simple user - returning not implemented for now
            return response('TODO - The user is not registered with the application', 501);
        }

        // get the role (checking if the user have permission to use the role)
        if (!($roleObject = $user->applications[0]->pivot->getRole($role))) {
            return response(['message' => "You don't have the privileges to login as this role"], 401);
        }

        // generate the token and respond
        return response($this->authManager->login($application, $user, $roleObject->role));
    }
}
