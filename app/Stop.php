<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stop extends Model
{
    protected $fillable = ['name'];

    public function route()
    {
        return $this->belongsToMany('App\Route', 'route_stop');
    }

    public function departure()
    {
        return $this->hasMany('App\Departure');
    }
}
