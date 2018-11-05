<?php

namespace App\Http\Controllers;

use App\Models\ApplicationUserRole;
use App\Models\Role;
use App\Models\User;
use App\Models\UserEmail;
use App\Services\Auth\Contracts\AuthManagerContract;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    protected $authManager;

    public function __construct(AuthManagerContract $authManager)
    {
        $this->authManager = $authManager;
    }

    public function index()
    {
        return User::with(['emails', 'socialAccounts'])->get();
    }

    public function indexPaginated(Request $request)
    {

        $pages = $request->query('pages') ?? 20;

        return User::with(['emails', 'socialAccounts'])->paginate($pages);
    }

    public function show($id)
    {

        $user = User::with(['emails', 'socialAccounts'])->find($id);

        if (!$user) {
            return response(['message' => 'User not found'], 404);
        }

        $application = $this->authManager->getApplication();

        $user->roles = $user->applications()->where(['app_name'=>$application])->first()->pivot->roles;
        return $user;
    }

    public function store(Request $request)
    {

        if (empty($allowedRoles = $this->authManager->getChildRoles())) {
            return response(['message' => 'Unauthorized'], 401);
        }

        $application = $this->authManager->getApplication();

        $this->validate($request, [
            'email' => 'required|email|unique:user_emails,email',
            'password' => 'required',
            'role' => [
                'required',
                function ($attribute, $value, $fail) use ($application) {
                    $role = Role::where(['role' => $value])
                        ->with(['applications' => function ($query) use ($application) {
                            $query->where(['app_name' => $application]);
                        }])
                        ->first();

                    if (!$role->applications->count()) {
                        return $fail("$attribute not allowed to this application");
                    }
                },
                Rule::in($allowedRoles)
            ]
        ]);

        if (!($user = $this->authManager->registerUser($request->email, $request->password, $request->role))) {
            return response(['message' => 'Error creating the user'], 500);
        }

        return response($user, 201);
    }

    public function addEmails(Request $request, $id)
    {

        if (empty($this->authManager->getChildRoles())) {
            return response(['message' => 'Unauthorized'], 401);
        }

        if (!User::find($id)) {
            return response(['message' => 'User not found'], 404);
        }

        $this->validate($request, [
            'emails' => 'required|array',
            'emails.*' => 'email|unique:user_emails,email'
        ], [
            'emails.*.email' => ':input is not a valid email',
            'emails.*.unique' => ':input has already been taken'
        ]);

        $return = [];

        foreach ($request->emails as $newEmail) {
            $emailObject = new UserEmail();
            $emailObject->email = $newEmail;
            $emailObject->user_id = $id;
            $emailObject->save();
            $return[] = $emailObject;
        }

        return response($return, 201);

    }

    public function removeEmails(Request $request, $id)
    {

        if (empty($this->authManager->getChildRoles())) {
            return response(['message' => 'Unauthorized'], 401);
        }

        if (!User::find($id)) {
            return response(['message' => 'User not found'], 404);
        }

        $this->validate($request, [
            'emails' => 'required|array',
            'emails.*' => [
                'email',
                Rule::exists('user_emails', 'email')->where(function($query) use ($id) {
                    $query->where(['user_id' => $id]);
                })
            ]
        ], [
            'emails.*.email' => ':input is not a valid email',
            'emails.*.exists' => ':input is not attached to the user'
        ]);

        app('db')->beginTransaction();

        UserEmail::whereIn('email', $request->emails)->delete();

        if (!UserEmail::where(['user_id' => $id])->count()) {
            app('db')->rollBack();
            return response(['message' => 'You can\'t remove all the emails of an user'], 400);
        }

        app('db')->commit();

        return response(null, 204);

    }

    public function changePassword(Request $request, $id)
    {
        if (empty($this->authManager->getChildRoles())) {
            return response(['message' => 'Unauthorized'], 401);
        }

        $user = User::find($id);

        if (!$user) {
            return response(['message' => 'User not found'], 404);
        }

        $this->validate($request, [
            'password' => 'required'
        ]);


        $user->password = app('hash')->make($request->password);
        $user->save();

        return response(null, 204);
    }

    public function grant(Request $request, $id)
    {

        if (empty($allowedRoles = $this->authManager->getChildRoles())) {
            return response(['message' => 'Unauthorized'], 401);
        }

        if (!User::find($id)) {
            return response(['message' => 'User not found'], 404);
        }

        $application = $this->authManager->getApplication();

        $this->validate($request, [
            'role' => [
                'bail',
                'required',
                'exists:roles,role',
                function ($attribute, $value, $fail) use ($application, &$role) {
                    $role = Role::where(['role' => $value])
                        ->with(['applications' => function ($query) use ($application) {
                            $query->where(['app_name' => $application]);
                        }])
                        ->first();

                    if (!$role || !$role->applications->count()) {
                        return $fail("The role is not allowed to this application");
                    }
                },
                Rule::in($allowedRoles)
            ]
        ]);

        $applicationUserRole = new ApplicationUserRole();
        $applicationUserRole->application_id = $role->applications->first()->id;
        $applicationUserRole->user_id = $id;
        $applicationUserRole->role_id = $role->id;
        $applicationUserRole->default = false;
        $applicationUserRole->save();

        return response(null, 204);
    }

    public function revoke(Request $request, $id)
    {
        if (empty($allowedRoles = $this->authManager->getChildRoles())) {
            return response(['message' => 'Unauthorized'], 401);
        }

        /** @var User $user */
        $user = User::find($id);

        if (!$user) {
            return response(['message' => 'User not found'], 404);
        }

        $applicationName = $this->authManager->getApplication();
        $application = $user->applications()->where(['app_name'=>$applicationName])->first();

        $roles = $application->pivot->roles;

        $this->validate($request, [
            'role' => [
                'bail',
                'required',
                Rule::in($roles->map(function ($item) {
                    return $item->role;
                })),
                Rule::in($allowedRoles)
            ]
        ]);

        ApplicationUserRole::where([
            'application_id' => $application->id,
            'user_id' => $id,
            'role_id' => $roles->filter(function ($item) use ($request) {
                return $item->role == $request->role;
            })->first()->id
        ])->delete();

        return response(null, 204);

    }

    public function destroy($id)
    {
        // TODO - Destroy everything
        User::destroy($id);
        return "User $id deleted.";
    }

    public function byRole($role)
    {
        return User::byRole($role, $this->authManager->getApplication())->get();
    }
}
