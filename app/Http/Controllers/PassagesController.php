<?php

namespace App\Http\Controllers;

use App\Departure;
use App\FreeDay;
use App\Passage;
use App\Stop;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PassagesController extends Controller
{
    public function getPassages($from, $to, $time, $date, $year)
    {
        $time = explode(':', $time);
        $date = explode('-', $date);
        $dayOfWeek = Carbon::createFromDate($year, $date[1], $date[0])->dayOfWeek;
        if ($dayOfWeek == 0) {
            $dayOfWeek = 7;
        }
        $passages = Passage::with(['departures', 'days', 'free_days', 'company'])
            ->whereHas('departures', function($query) use ($from, $time) {
                $query->where('stop_id', $from)->where('hours', '>', $time[0])
                    ->orWhere(function ($query) use ($time) {
                        $query->where('hours', '=', $time[0]);
                        $query->where('minutes', '>=', $time[1]);
                    });
            })
            ->whereHas('departures', function($query) use ($to) {
                $query->where('stop_id', $to);
            })
            ->whereHas('days', function($query) use ($dayOfWeek) {
                $query->where('id', '=', $dayOfWeek);
            })
            ->whereDoesntHave('free_days', function($query) use ($date) {
                $query->where('day', '=', $date[0])
                    ->Where('month', '=', $date[1]);
            })
            ->get();
        $passages = $passages->reject(function ($passage) use ($to, $from) {
            $fromIndex = 0;
            $toIndex = 0;
            foreach ($passage['departures'] as $departure) {
                if ($departure['stop_id'] == $from) {
                    $fromIndex = $departure['index'];
                } else if ($departure['stop_id'] == $to) {
                    $toIndex = $departure['index'];
                }
            }
            return $fromIndex > $toIndex;
        });
        return $passages->values()->all();
    }

    public function getUserPassages($company_id)
    {
        $passages = auth()->user()->passages()->with('departures')->get();
        return response()->json($passages);
    }

    public function getPassage($id)
    {
        $passage = Passage::with('departures', 'free_days', 'days')->find($id);
        return response()->json($passage);
    }

    public function editPassage(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            //error_log(1);
            $passage = auth()->user()->passages->find($id);
            $passage->update($request->all());
            $passage->days()->sync($request->days);
            $passage->free_days()->delete();
            foreach ($request->get('freeDays') as $key => $value) {
                $freeDay = new FreeDay();
                $freeDay->day = $value['day'];
                $freeDay->month = $value['month'];
                $passage->free_days()->save($freeDay);
            }
            $passage->departures()->delete();
            foreach ($request->get('stops') as $data) {
                $departure = new Departure();
                $departure->index = $data['index'];
                $departure->name = $data['name'];
                $departure->hours = $data['hours'];
                $departure->minutes = $data['minutes'];
                if (!array_key_exists('id', $data)) {
                    $newStop = new Stop();
                    $newStop->name = $data['name'];
                    $newStop->save();
                    $departure->stop()->associate($newStop->id);
                    $departure->passage()->associate($passage->id);
                    $departure->save();
                } else {
                    $departure->passage()->associate($passage->id);
                    $departure->stop()->associate($data['stop_id']);
                    $departure->save();
                }
            }
            return response()->json($passage);
        });
    }

    public function createPassage(Request $request)
    {
        try {
            $passage = new Passage();
            DB::transaction(function () use ($request, $passage) {
                $passage->price = $request->get('price');
                $passage->from = $request->get('from');
                $passage->to = $request->get('to');
                $passage->user()->associate(auth()->user()->id);
                $passage->company()->associate(auth()->user()->company->id);
                $passage->save();
                $passage->days()->attach($request->days);

                foreach ($request->get('freeDays') as $key => $value) {
                    $freeDay = new FreeDay();
                    $freeDay->day = $value['day'];
                    $freeDay->month = $value['month'];
                    $passage->free_days()->save($freeDay);
                }

                foreach ($request->get('stops') as $data) {
                    $departure = new Departure();
                    $departure->index = $data['index'];
                    $departure->name = $data['name'];
                    $departure->hours = $data['hours'];
                    $departure->minutes = $data['minutes'];
                    if (!array_key_exists('id', $data)) {
                        $newStop = new Stop();
                        $newStop->name = $data['name'];
                        $newStop->save();
                        $departure->stop()->associate($newStop->id);
                        $departure->passage()->associate($passage->id);
                        $departure->save();
                    } else {
                        $departure->stop_id = $data['stop_id'];
                        $departure->passage_id = $passage->id;
                        $departure->save();
                    }
                }
            });
        }
        catch (Exception $e) {
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
        $passage->days()->detach();
        $res = $passage->delete();
        return response()->json($res);
    }

}
