<?php

namespace App\Http\Controllers;

use App\Role;
use Exception;
use HttpException;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminsController extends Controller
{
    public function getUsers()
    {
        return response()->json(['auth' => Auth::user(), 'users' => User::all()->except(Auth::id())]);
    }

    public function getUser($id) {
        return User::find($id);
    }

    public function editUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());

        return $user;
    }

    public function createUser(Request $request)
    {

        try {
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->login = $request->input('login');
            $user->password = Hash::make($request->input('pass'));
            $user->save();
            $role = Role::where('name', '=', 'user')->first();
            $user->roles()->attach($role->id);

        } catch (Exception $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([$e->errorInfo[2]], 409);
            }
            return response()->json([$e->errorInfo[2]], 500);
        }

        return response()->json($user->id);
    }

    public function createStop(Request $request) {

    }


}
