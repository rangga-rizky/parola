<?php

namespace App\Helper;
use App\TrainingTerm;
use DB;

class CorrelationMeassure
{
    public function dotProduct($training,$testing){

        for ($i=0; $i < sizeof($training); $i++) { 
            $dotproduct = 0;
            for ($j=0; $j < sizeof($testing); $j++) { 
                $dotproduct = $dotproduct + ($testing[$j] * $training[$i][$j+1]);
            }
            $results[$i]["hasil"] = $dotproduct;
            $results[$i]["class"] = $training[$i][0];
        }
        usort($results, function($a, $b) {
            return ($a['hasil'] > $b['hasil']) ? -1 : 1;
        });
        return $results[0]["class"];
    }

    public function cosineSimilarity($training,$testing){
         for ($i=0; $i < sizeof($training); $i++) { 
            $dotproduct = 0;
            $sumOfAbsTesting = 0;
            $sumOfAbstraining = 0;
            for ($j=0; $j < sizeof($testing); $j++) { 
                $sumOfAbsTesting = $sumOfAbsTesting + $testing[$j] * $testing[$j];
                $sumOfAbstraining = $sumOfAbstraining + $training[$i][$j+1] * $training[$i][$j+1];
                $dotproduct = $dotproduct + ($testing[$j] * $training[$i][$j+1]);
            }
            if($sumOfAbsTesting  * $sumOfAbstraining > 0){                
                 $results[$i]["hasil"] = $dotproduct / ($sumOfAbsTesting  * $sumOfAbstraining);
            }else{
                $results[$i]["hasil"] = 0;
            }
                
            $results[$i]["class"] = $training[$i][0];
        }
        usort($results, function($a, $b) {
            return ($a['hasil'] > $b['hasil']) ? -1 : 1;
        });
        return $results[0]["class"];
    }

    public function buildCorrMatrice($distinct_terms,$termsPerCategory,$categories){      
        $fitur = $distinct_terms; 
        array_unshift($fitur , ' '); 
        $matriks[] = $fitur;
        foreach ($categories as $category) {            
            $terms = $termsPerCategory[$category["category"]];
            $association_word[0] = $category["category"];
            for ($i=0; $i < sizeof($distinct_terms) ; $i++) { 
                for ($j=0; $j < sizeof($terms); $j++) { 
                    if($terms[$j] == $distinct_terms[$i]){
                        $association_word[$i+1] = 1;
                        break;
                    }else{
                        $association_word[$i+1] = 0;
                    }   
                }               
            }
            $matriks[] = $association_word;
        }

        return $matriks;
    }

    public function buildWordProprotionMatrice(TrainingTerm $trainingTerm,$categories){      
        $fitur = $trainingTerm->select('term', DB::raw('count(*) as total'))->orderBy("term")->groupBy("term")->pluck('term')->toArray();
        array_unshift($fitur , ' '); 
        $matriks[] = $fitur;    
        foreach ($categories as $category) {            
            $terms = $trainingTerm->where('category_id',$category->id)->get();
            $association_word[0] = $category["category"];
            $sumScore = $terms->sum('score');
            for ($i=1; $i < sizeof($fitur) ; $i++) { 
                for ($j=0; $j < sizeof($terms); $j++) { 
                    if($terms[$j]->term == $fitur[$i]){
                        $association_word[$i] = $terms[$j]->score/$sumScore;
                        break;
                    }else{
                        $association_word[$i] = 0;
                    }   
                }              
            }
            $matriks[] = $association_word;
        }
        return $matriks;
    }
 

   

}
