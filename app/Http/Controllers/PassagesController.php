<?php

namespace App\Http\Controllers;

use App\Departure;
use App\FreeDay;
use App\Passage;
use App\Role;
use App\Stop;
use App\Route;
use Carbon\Carbon;
use DateTime;
use Exception;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

Builder::macro('whereLike', function ($attributes, string $searchTerm) {
    $this->where(function (Builder $query) use ($attributes, $searchTerm) {
        foreach (array_wrap($attributes) as $attribute) {
            $query->when(
                str_contains($attribute, '.'),
                function (Builder $query) use ($attribute, $searchTerm) {
                    [$relationName, $relationAttribute] = explode('.', $attribute);

                    $query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm) {
                        $query->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
                    });
                },
                function (Builder $query) use ($attribute, $searchTerm) {
                    $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                }
            );
        }
    });

    return $this;
});


class PassagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Passage::find(25)->with(['company', 'departure'])->orderBy('id', 'desc')->get();
    }

    public function getPassages($from, $to, $time, $date, $year)
    {
        $fromPassageIds = [];
        $equalPassageIds = [];
        $time = explode(':', $time);
        $date = explode('-', $date);
        error_log($time[0].':'.$time[1]);
        foreach (Stop::with(['departure'])->find($from)['departure'] as $data) {
            $hours = date('H', strtotime($data['time']));
            $minutes = date('i', strtotime($data['time']));
            if ($hours > $time[0] || ($hours == $time[0] && $minutes >= $time[1])) {
                array_push($fromPassageIds, $data['passage_id']);
            }
        }
        foreach (Stop::with(['departure'])->find($to)['departure'] as $data) {
            $hours = date('H', strtotime($data['time']));
            $minutes = date('i', strtotime($data['time']));
            if ($hours > $time[0] || ($hours == $time[0] && $minutes >= $time[1])) {
                if (in_array($data['passage_id'], $fromPassageIds)) {
                    array_push($equalPassageIds, $data['passage_id']);
                }
            }
        }
        $passages = Passage::with(['departure', 'day', 'freeDay', 'company'])->find($equalPassageIds);

        $passages = $passages->reject(function ($passage) use ($to, $from, $date, $year) {
            $fromIndex = 0;
            $toIndex = 0;
            foreach ($passage['departure'] as $departure) {
                if ($departure['stop_id'] == $from) {
                    $fromIndex = $departure['index'];
                } else if ($departure['stop_id'] == $to) {
                    $toIndex = $departure['index'];
                }
            }
            foreach ($passage['freeDay'] as $freeDay) {
                $timestamp = strtotime($freeDay['day']);
                $day = date('d', $timestamp);
                $month = date('m', $timestamp);
                if ($day == $date[0] && $month == $date[1]) {
                    return true;
                }
            }
            $dayOfWeek = Carbon::createFromDate($year, $date[1], $date[0])->dayOfWeek;
            if ($dayOfWeek == 0) {
                $dayOfWeek = 7;
            }
            $dayNotFound = true;
            foreach ($passage['day'] as $day) {
                if ($day['id'] == $dayOfWeek) {
                    $dayNotFound = false;
                }
            }
            if ($dayNotFound) {
                return true;
            }
            //error_log($dayOfWeek);
            return $fromIndex > $toIndex;
        });
        return response()->json($passages->values());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Passage::create($request->all());

        return response()->json([
            'created' => true
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        Log::info(Passage::find($id)->join('companies', 'companies.id', '=', 'passages.company_id')->get());
        return Passage::find($id)->join('companies', 'companies.id', '=', 'passages.company_id')->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
