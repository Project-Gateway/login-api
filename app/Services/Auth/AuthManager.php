<?php

namespace App\Services\Auth;

use App\Models\Application;
use App\Models\ApplicationUser;
use App\Models\ApplicationUserRole;
use App\Models\Role;
use App\Models\SocialAccount;
use App\Models\User;
use App\Models\UserEmail;
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

    /**
     * @var string
     */
    protected $application = null;

    public function __construct(TokenFactoryContract $tokenFactory, WhitelistContract $whitelist)
    {
        $this->tokenFactory = $tokenFactory;
        $this->whiteList = $whitelist;
    }

    public function login(ApplicationContract $application, UserContract $user, Role $role): array
    {
        $emails = $user->getAllEmails();
        $databaseRole = $application->getName() . '_' . $role;
        $token = $this->tokenFactory->build($application->getName(), $user->getId(), [
            'emails' => $emails,
            'dbRole'=> $databaseRole,
            'role' => $role->role,
            'childRoles' => $role->children->map(function($item) {
                return $item->role;
            }),
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => $user->phone
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
            'accessToken' => (string)$token,
            'tokenType' => 'bearer',
            'expiresIn' => $token->getClaimValue('exp') - time(),
            'emails' => $emails,
        ];
    }

    public function getToken(): ?TokenContract
    {
        return $this->token;
    }

    public function setToken(TokenContract $token): void
    {
        $this->token = $token;
    }

    public function getApplication(): string
    {
        return $this->application;
    }

    public function setApplication(string $application): void
    {
        $this->application = $application;
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

    public function registerUser(string $email, string $password = null, string $role = null, $socialProvider = null, $socialId = null, $avatar = null): ?UserContract
    {
        app('db')->beginTransaction();
        try {

            // creates the user
            $user = new User();
            $user->password = $password ? app('hash')->make($password) : null;
            $user->save();

            // creates the userEmail entry, linked to the user
            $userEmail = new UserEmail();
            $userEmail->fill([
                'user_id' => $user->id,
                'email' => $email
            ]);
            $userEmail->save();

            // finds the application with the default role for registration
            $application = Application::byName($this->application)->with(['roles' => function ($query) use ($role) {
                if (!$role) {
                    $query->where(['default' => true]);
                } else {
                    $query->where(['role' => $role]);
                }
            }])->first();

            // links the user to the application
            $applicationUser = new ApplicationUser();
            $applicationUser->application_id = $application->id;
            $applicationUser->user_id = $user->id;
            $applicationUser->save();

            // links the user/application to the role
            $applicationUserRole = new ApplicationUserRole();
            $applicationUserRole->application_id = $application->id;
            $applicationUserRole->user_id = $user->id;
            $applicationUserRole->role_id = $application->roles->first()->id;
            $applicationUserRole->default = true;
            $applicationUserRole->save();

            // if it's a social register, creates the social account entry
            if ($socialProvider) {

                $socialAccount = new SocialAccount();
                $socialAccount->fill([
                    'user_id' => $user->id,
                    'provider' => $socialProvider,
                    'social_id' => $socialId,
                    'avatar' => $avatar,
                ]);
                $socialAccount->save();
            }

        } catch (\Exception $e) {
            app('db')->rollBack();
            //throw $e;
            // TODO - log the exception
            return null;
        }

        app('db')->commit();
        return $user;
    }

    public function getUserId(): string
    {
        return $this->token->getClaimValue('sub');
    }

    public function getRole(): string
    {
        return preg_replace("/^{$this->application}_/", '', $this->token->getClaimValue('role'));
    }

    public function getChildRoles(): array
    {
        /** @var Role $loggedRole */
        $loggedRole = Role::findByRoleName($this->getRole());
        if (!$loggedRole->can_create_users) {
            return [];
        }

        return $loggedRole->children->map(function ($item) {
            return $item->role;
        })->toArray();
    }

}
