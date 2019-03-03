<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Departure extends Model
{


    public function passage()
    {
        return $this->belongsTo('App\Passage');
    }
}
