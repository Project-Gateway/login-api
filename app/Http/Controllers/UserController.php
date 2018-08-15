<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function index()
    {
        return User::with(['emails', 'socialAccounts'])->get();
    }

    public function indexPaginated(Request $request)
    {

        $pages = $request->query->pages ?? 20;

        return User::with(['emails', 'socialAccounts'])->paginate($pages);
    }

    public function show($id)
    {
        $user = User::with(['emails', 'socialAccounts'])->find($id);
        return $user;
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'application' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required'
        ]);

        $newUser = new User();
        $newUser->fill($request->all());
        $newUser->save();
        return $newUser;
    }

    public function update(Request $request, $id)
    {
        // validate the request

        $user = User::find($id);
        $user->fill($request->all());
        $user->save();
        return $user;
    }

    public function destroy($id)
    {
        User::destroy($id);
        return "User $id deleted.";
    }
}
