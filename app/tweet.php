<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class tweet extends Model
{
     public $timestamps = False; 

     protected $fillable = [
        'username', 'tweet','date',"timestamp","clean_tweet","predicted"
    ];

     public function getDateTimeLocalized()
    {
        setlocale(LC_TIME, 'id_ID.UTF-8');
        return Carbon::parse($this->attributes['date'])->formatLocalized('%H:%S %A, %d %B %Y');
    }

}
