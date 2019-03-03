<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{


    public function departure()
    {
        return $this->hasOne('App\Passage');
    }
}
