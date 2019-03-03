<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Passage extends Model
{
    protected $fillable = ['price', 'company_id'];

    public function company()
    {
        return $this->belongsTo('App\Company');
    }


    public function departure()
    {
        return $this->hasMany('App\Departure');
    }
}
