<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Passage extends Model
{
    protected $fillable = ['price', 'from', 'to'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function days()
    {
        return $this->belongsToMany(Day::class, 'day_passage');
    }

    public function free_days()
    {
        return $this->hasMany(FreeDay::class);
    }

    public function departures()
    {
        return $this->hasMany(Departure::class)->orderBy('index', 'asc');
    }
}
