<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = ['from', 'to'];

    public function stops()
    {
        return $this
            ->belongsToMany(Stop::class, 'template_stop')
            ->withPivot('order')
            ->orderBy('order');
    }
}
