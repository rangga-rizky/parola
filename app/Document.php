<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
	public $timestamps = False; 
    protected $fillable = [
        'predicted','clean_complaint','words'
    ];
}
