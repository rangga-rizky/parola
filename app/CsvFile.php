<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CsvFile extends Model
{
    protected $fillable = [
        'path', 'project_id',"type"
    ];
}
