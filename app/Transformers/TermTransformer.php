<?php

namespace App\Transformers;


class TermTransformer extends Transformer {

    public function transform($data)
    {        
        
        return [
            'id' => $data['id'],
            'term' => $data['term'],  
            'category_id' => $data['category_id'],
            'score' => $data['score'],
            'category' => $data->category['category'],   
        ];
    }
}
