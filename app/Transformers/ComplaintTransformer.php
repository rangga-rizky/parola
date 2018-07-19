<?php

namespace App\Transformers;


class ComplaintTransformer extends Transformer {

    public function transform($data)
    {        
        
        return [
            'id' => $data['id'],
            'username' => $data['username'],  
            'tweet' => $data['tweet'],   
            'predicted' => $data['predicted'],   
            'date' => $data->getDateTimeLocalized(),   
        ];
    }
}
