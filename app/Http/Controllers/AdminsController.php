<?php

namespace App\Http\Controllers;

use App\Company;
use App\Role;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminsController extends Controller
{
    public function getUsers()
    {
        return response()->json(['auth' => Auth::user(), 'users' => User::all()->except(Auth::id())]);
    }

    public function getCompanies()
    {
        $companies = DB::table('companies')
            ->join('passages', 'companies.id', '=', 'passages.company_id')
            ->select('name', DB::raw('count(*) as numberPassages'))
            ->groupBy('name')
            ->get();
        return $companies;
    }

    public function getUser($id)
    {
        return User::find($id);
    }

    public function editUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        $user->company()->update(['name' => $user->name]);
        return $user;
    }

    public function changeUserPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update(['password' => Hash::make($request->input('password'))]);
        return $user;
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
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

            $company = new Company();
            $company->name = $request->input('name');
            $user->company()->save($company);
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

}
