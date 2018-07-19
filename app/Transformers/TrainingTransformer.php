<?php

namespace App\Transformers;


class TrainingTransformer extends Transformer {

    public function transform($data)
    {        
        
        return [
            'id' => $data['id'],
            'pengaduan' => $data['complaint'],  
            'label' => $data['category'],
            'words' => $data['words'],
            'manual_words' => $data['manual_words'],
            'prediksi_bayes' => $data['bayes_predicted'],
            'prediksi_korelasi' => $data['predicted'], 
            'prediksi_manual' => $data['manual_predicted'],   
        ];
    }
}
