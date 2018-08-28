<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
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

        // get the application name
        $applicationName = $this->authManager->getApplication();

        $message = 'Invalid username or password';

        // email/user exists? If yes, get the email linked with the user and current application
        $email = UserEmail::where(['email' => $request->get('email')])
            ->with(['user.applications' => function ($query) use ($applicationName) {
                $query->where(['app_name' => $applicationName]);
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
        return $this->checkUserPermissions($email->user, $request->get('role') ?? null);

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
        // get the application name
        $applicationName = $this->authManager->getApplication();

        /** @var SocialiteUser $socialiteUser */
        $socialiteUser = $this->authManager->retrieveSocialUser($provider, app('request')->query('redirectUri'));

        // check if the user is already registered with this social account
        $socialAccount = SocialAccount::where(['social_id' => $socialiteUser->getId()])
            ->with(['user.applications' => function ($query) use ($applicationName) {
                $query->where(['app_name' => $applicationName]);
            }])
            ->first();
        if ($socialAccount) {

            return $this->checkUserPermissions($socialAccount->user, $request->get('role'));

        }

        // user not registered, but email exists. Respond with error.
        if (UserEmail::where(['email' => $socialiteUser->getEmail()])->count()) {
            return response(['message' => "Email {$socialiteUser->getEmail()} already used by another account."], 401);
        }

        // register new user
        if (!($user = $this->authManager->registerUser($socialiteUser->getEmail(), null, null, $provider, $socialiteUser->getId(), $socialiteUser->getAvatar()))) {
            return response(['message' => 'Can\'t create the account'], 500);
        }

        return $this->checkUserPermissions($user);

    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:user_emails,email',
            'password' => 'required',
        ]);

        if (!($user = $this->authManager->registerUser($request->get('email'), $request->get('password')))) {
            return response(['message' => 'Can\'t create the account'], 500);
        }

        return $this->checkUserPermissions($user);

    }

    protected function checkUserPermissions($user, $role = null)
    {
        $application = $user->applications->first() ?? null;

        // Isn't the user linked to the application?
        if (!$application) {
            // TODO - Register the user with the application, as a simple user - returning not implemented for now
            return response('TODO - The user is not registered with the application', 501);
        }

        // get the role (checking if the user have permission to use the role)
        if (!($roleObject = $application->pivot->getRole($role))) {
            return response(['message' => "You don't have the privileges to login as this role"], 401);
        }

        // generate the token and respond
        return response($this->authManager->login($application, $user, $roleObject));
    }
}
