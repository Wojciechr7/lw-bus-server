<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Departure extends Model
{
    protected $fillable = ['index', 'name', 'time', 'stop_id'];

    public function passage()
    {
        return $this->belongsTo('App\Passage');
    }

    public function stop()
    {
        return $this->belongsTo('App\Stop');
    }
}
