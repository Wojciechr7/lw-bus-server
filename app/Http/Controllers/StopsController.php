<?php

namespace App\Http\Controllers;

use App\Stop;
use Illuminate\Http\Request;

class StopsController extends Controller
{
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

    public function removeStop(Request $request, $id)
    {
        $res = Stop::where('id', $id)->delete();
        return $res;
    }

    public function getStops()
    {
        return response()->json(Stop::all());
    }
}
