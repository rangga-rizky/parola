<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainingTerm extends Model
{
    public $table = "training_terms";

    protected $fillable = [
        'term', 'category_id','score'
    ];

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function scopeByCategory($category_id){
    	return $this->where("category_id",$category_id);
    }
}
