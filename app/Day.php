<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    public function passage()
    {
        return $this->belongsToMany('App\Passage', 'day_passage');
    }
}
