<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
     public function categoryObject()
    {
        return $this->belongsTo('App\Category','category_id');
    }

     public function project()
    {
        return $this->belongsTo('App\Project');
    }
}
