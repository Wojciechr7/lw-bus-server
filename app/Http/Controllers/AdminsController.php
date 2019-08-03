<?php

namespace App\Http\Controllers;

use App\Role;
use App\Stop;
use App\Route;
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
        try {
            $stop = new Stop();
            $stop->name = $request->input('name');
            $stop->save();
        } catch (Exception $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([$e->errorInfo[2]], 409);
            }
            return response()->json([$e->errorInfo[2]], 500);
        }
        return response()->json($stop);
    }

    public function createRoute(Request $request) {
        try {
            $route = new Route();
            $route->from = $request->input('from');
            $route->to = $request->input('to');
            $route->save();
            $route->stop()->attach($request->stopIds);
        } catch (Exception $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([$e->errorInfo[2]], 409);
            }
            return response()->json([$e->errorInfo[2]], 500);
        }
        return response()->json($route);
    }

    public function getRoutes() {
        return response()->json(Route::with('stop')->get());
    }

    public function getRoute($id) {
        return response()->json(Route::with('stop')->get()->find($id));
    }

    public function editRoute(Request $request, $id) {
        $route = Route::findOrFail($id);
        $route->update($request->all());
        $route->stop()->detach();
        $route->stop()->attach($request->stopIds);

        return $route;
    }

    public function removeStop(Request $request, $id) {
        $res = Stop::where('id', $id)->delete();
        return $res;
    }

    public function getStops() {
        return response()->json(Stop::all());
    }


}
