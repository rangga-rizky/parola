<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Tweet extends Model
{
     public $timestamps = False; 

     protected $fillable = [
        'username', 'tweet','date',"timestamp","clean_tweet","predicted","words"
    ];

     public function getDateTimeLocalized()
    {
        setlocale(LC_TIME, 'id_ID.UTF-8');
        return Carbon::parse($this->attributes['date'])->formatLocalized('%H:%S %A, %d %B %Y');
    }

}
