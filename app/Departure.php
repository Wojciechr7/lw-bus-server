<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Departure extends Model
{
    protected $fillable = ['index', 'name', 'hours', 'minutes'];

    public function passage()
    {
        return $this->belongsTo(Passage::class);
    }

    public function stop()
    {
        return $this->belongsTo(Stop::class);
    }
}
