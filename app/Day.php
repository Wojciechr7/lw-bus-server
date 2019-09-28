<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    public function passages()
    {
        return $this->belongsToMany(Passage::class, 'day_passage');
    }
}
