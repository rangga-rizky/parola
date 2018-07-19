<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function terms()
    {
        return $this->hasMany('App\Term');
    }

     public function trainings()
    {
        return $this->hasMany('App\Training');
    }
}
