<?php

namespace App\Http\Controllers;

use App\Company;
use App\Departure;
use App\FreeDay;
use App\Passage;
use App\Role;
use App\Stop;
use App\Route;
use Exception;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminsController extends Controller
{
    public function getUsers()
    {
        return response()->json(['auth' => Auth::user(), 'users' => User::all()->except(Auth::id())]);
    }

    public function getUser($id)
    {
        return User::find($id);
    }

    public function editUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
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

        /*$user->roles()->detach();*/
        $company = Company::findOrFail($user['company_id']);
        $company->delete();
        $user->delete();
        /*$company = Company::findOrFail($user['company_id']);
        $company->delete();
        $passages = Passage::where('company_id', '=', $user['company_id']);
        $passages->delete();
        $user->delete();*/
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
            $company = new Company();
            $company->name = $request->input('name');
            $company->save();
            $user->company_id = $company['id'];
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

    public function createStop(Request $request)
    {
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

    public function createRoute(Request $request)
    {
        try {
            $route = new Route();
            $route->from = $request->input('from');
            $route->to = $request->input('to');
            $route->order = $request->input('order');
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

    public function getRoutes()
    {
        $routes = Route::with('stop')->get();
        foreach ($routes as $r) {
            $r->from = Stop::findOrFail($r->from);
            $r->to = Stop::findOrFail($r->to);
        }
        return response()->json($routes);
    }

    public function getRoute($id)
    {
        $route = Route::with('stop')->get()->find($id);
        $route->from = Stop::findOrFail($route->from);
        $route->to = Stop::findOrFail($route->to);
        return response()->json($route);
    }

    public function editRoute(Request $request, $id)
    {
        $route = Route::findOrFail($id);
        $route->update($request->all());
        $route->stop()->sync($request->stopIds);

        return $route;
    }

    public function removeStop(Request $request, $id)
    {
        $res = Stop::where('id', $id)->delete();
        return $res;
    }

    public function deleteRoute(Request $request, $id)
    {
        $res = Route::where('id', $id)->delete();
        return $res;
    }

    public function getStops()
    {
        return response()->json(Stop::all());
    }

    public function getPassages($company_id)
    {
        $passages = Passage::where('company_id', $company_id)->with('departure')->get();
        return response()->json($passages);
    }

    public function getPassage($id)
    {
        $passage = Passage::where('id', $id)->with('departure', 'freeDay', 'day')->first();
        return response()->json($passage);
    }

    public function editPassage(Request $request, $id)
    {
        //error_log(1);
        $passage = Passage::findOrFail($id);
        $passage->update($request->all());
        $passage->day()->sync($request->days);
        $passage->freeDay()->delete();
        foreach ($request->get('freeDays') as $key => $value) {
            $freeDay = new FreeDay();
            $freeDay->day = $value;
            $freeDay->save();
            $passage->freeDay()->attach($freeDay->id);
        }
        $passage->departure()->delete();
        foreach ($request->get('stops') as $data) {
            $departure = new Departure();
            $departure->index = $data['index'];
            $departure->name = $data['name'];
            $departure->time = new \DateTime($data['time']);
            $departure->passage_id = $passage->id;
            if (!array_key_exists('stop_id', $data)) {
                $newStop = new Stop();
                $newStop->name = $data['name'];
                $newStop->save();
                $departure->stop_id = $newStop->id;
            } else {
                $departure->stop_id = $data['stop_id'];
            }
            $departure->save();
        }
        return $passage;
    }

    public function createPassage(Request $request)
    {
        try {
            //error_log($request->get('days'));
            $passage = new Passage();
            $passage->price = $request->get('price');
            $passage->from = $request->get('from');
            $passage->to = $request->get('to');
            $passage->company_id = $request->get('company_id');
            $passage->save();
            $passage->company()->attach($request->get('company_id'));
            $passage->day()->attach($request->days);
            foreach ($request->get('freeDays') as $key => $value) {
                $freeDay = new FreeDay();
                $freeDay->day = $value;
                $freeDay->save();
                $passage->freeDay()->attach($freeDay->id);
            }
            foreach ($request->get('stops') as $data) {
                $departure = new Departure();
                $departure->index = $data['index'];
                $departure->name = $data['name'];
                $departure->time = new \DateTime($data['time']);
                $departure->passage_id = $passage->id;
                if (!array_key_exists('stop_id', $data)) {
                    $newStop = new Stop();
                    $newStop->name = $data['name'];
                    $newStop->save();
                    $departure->stop_id = $newStop->id;
                } else {
                    $departure->stop_id = $data['stop_id'];
                }
                $departure->save();
            }
        } catch (Exception $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([$e->errorInfo[2]], 409);
            }
            return response()->json([$e->errorInfo[2]], 500);
        }
        return response()->json($passage);
    }

    public function deletePassage(Request $request, $id)
    {
        $passage = Passage::findOrFail($id);
        $passage->day()->detach();
        $passage->freeDay()->delete();
        $passage->departure()->delete();
        $res = $passage->delete();
        return response()->json($res);
    }


}
