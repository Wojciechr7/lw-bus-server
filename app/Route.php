<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = ['from', 'to', 'order'];

    public function stop()
    {
        return $this->belongsToMany('App\Stop', 'route_stop');
    }
}
