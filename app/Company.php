<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name'];

    public function departure()
    {
        return $this->hasOne('App\Passage');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function passage()
    {
        return $this->belongsToMany('App\Passage', 'company_passage');
    }
}
