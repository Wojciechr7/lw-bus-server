<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FreeDay extends Model
{
    protected $fillable = ['day', 'month'];

    public function passage()
    {
        return $this->belongsTo(Passage::class);
    }
}
