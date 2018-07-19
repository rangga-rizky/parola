<?php

namespace App\Helper;


use Illuminate\Support\Str;

class SlugGenerator
{
    public function generate($text,$model){
    	$slug = Str::slug($text);
        $count = $model->whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
        return $count ? "{$slug}-{$count}" : $slug;
    }
}
