<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Passage extends Model
{
    protected $fillable = ['price', 'company_id', 'from', 'to'];

    public function company()
    {
        return $this->belongsToMany('App\Company', 'company_passage');
    }

    public function departure()
    {
        return $this->hasMany('App\Departure');
    }

    public function freeDay()
    {
        return $this->belongsToMany('App\FreeDay', 'free_day_passage');
    }

    public function day()
    {
        return $this->belongsToMany('App\Day', 'day_passage');
    }
}
