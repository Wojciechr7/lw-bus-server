<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stop extends Model
{
    protected $fillable = ['name'];

    public function template()
    {
        return $this
            ->belongsToMany(Template::class, 'template_stop')
            ->withPivot('order')
            ->orderBy('order');
    }

    public function departures()
    {
        return $this->hasMany(Departure::class);
    }
}
