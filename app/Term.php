<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
     public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function project()
    {
        return $this->belongsTo('App\Project');
    }
}
