<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Testing extends Model
{
     public function category()
    {
        return $this->hasOne('App\Category');
    }
}
