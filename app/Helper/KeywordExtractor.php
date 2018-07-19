<?php

namespace App\Helper;

class KeywordExtractor
{

    public function aggregate($tokenized_documents,$distinct_terms){

        foreach ($distinct_terms as $distinct_term) {
            $topWords[$distinct_term] = 0;
        }
        
        foreach ($tokenized_documents as $document) {
            $words = [];
            for ($i=0; $i < sizeof($distinct_terms); $i++) { 
                if(in_array($distinct_terms[$i], $document)){
                    $topWords[$distinct_terms[$i]]++;
                }
            }
        }
    
        $nonZeroWods = array_filter($topWords, function($val) { return $val > 0; });
        $thresHolded = array_filter($topWords, function($val) use ($nonZeroWods) { return $val > array_sum($nonZeroWods)/sizeof($nonZeroWods); });   
        arsort($thresHolded);
        
        return $thresHolded;

    }

    public function aggregate_n($tokenized_documents,$distinct_terms,$n){

        foreach ($distinct_terms as $distinct_term) {
            $topWords[$distinct_term] = 0;
        }
        
        foreach ($tokenized_documents as $document) {
            $words = [];
            for ($i=0; $i < sizeof($distinct_terms); $i++) { 
                if(in_array($distinct_terms[$i], $document)){
                    $topWords[$distinct_terms[$i]]++;
                }
            }
        }
        
        
        arsort($topWords);
        $topWords = array_slice( $topWords, 0,$n);       
        
        return $topWords;

    }


}
