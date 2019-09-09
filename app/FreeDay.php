<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FreeDay extends Model
{
    protected $fillable = ['day'];

    public function passage()
    {
        return $this->belongsToMany('App\Passage', 'free_day_passage');
    }
}
