<?php

namespace App\Http\Controllers;

use App\Company;
use App\Permission;
use App\Role;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAuthenticateController extends Controller
{

    public function index()
    {
        return response()->json(['auth'=>Auth::user(), 'users'=>User::all()]);
    }

    public function test() {
        return [1, 2, 3];
    }


    public function authenticate(Request $request)
    {
        $credentials = $request->only('login', 'pass');

        $token = NULL;

        $user = \App\User::with('roles')->get()->where('login', '=', $request->input('login'))->first();

        try {
            if ($user && password_verify($request->input('pass'), $user->password)) {
                $token = JWTAuth::fromUser($user);
            } else {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        $user->company = Company::findOrFail($user->company_id);
        $user->token = $token;
        // if no errors are encountered we can return a JWT
        return response()->json($user);
    }

    public function createRole(Request $request){
        $role = new Role();
        $role->name = $request->input('name');
        $role->save();
        return response()->json("created");
    }

    public function createPermission(Request $request){
        $viewUsers = new Permission();
        $viewUsers->name = $request->input('name');
        $viewUsers->save();

        return response()->json("created");
    }

    public function assignRole(Request $request){
        $user = User::where('login', '=', $request->input('login'))->first();

        $role = Role::where('name', '=', $request->input('role'))->first();
        //$user->attachRole($request->input('role'));
        $user->roles()->attach($role->id);

        return response()->json("created");
    }

    public function attachPermission(Request $request){
        $role = Role::where('name', '=', $request->input('role'))->first();
        $permission = Permission::where('name', '=', $request->input('name'))->first();
        $role->attachPermission($permission);

        return response()->json("created");
    }

}
