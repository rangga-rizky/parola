<?php

namespace App\Helper;


class NaiveBayes
{
    public function  training($training,$labels,$terms){     

        $prob = array();
        for ($i=0; $i < sizeof($training) ; $i++) { 
            for ($j=0; $j < sizeof($labels) ; $j++) { 
                if($training[$i]["labeled_category"] ==  $labels[$j] ){
                    if(isset($numberOfClass[$j])){
                        $numberOfClass[$j]++;
                    }else{                      
                        $numberOfClass[$j] = 0;
                    }
                }
            }
        }
        
        for ($indexClass=0; $indexClass < sizeof($labels); $indexClass++) { 

            $wordProbOnClassX = array();
            $find = false;
            foreach ($terms as $term) {
                $sumTerm_inXclass = 0;
                $numberOfWord = 1;
                for ($indexTraining=0; $indexTraining < sizeof($training); $indexTraining++) { 
                    if($training[$indexTraining][$term] > 0 && $training[$indexTraining]["labeled_category"] == $labels[$indexClass]){
                        $numberOfWord = $numberOfWord + $training[$indexTraining][$term];                          
                    }   
                    $sumTerm_inXclass = $sumTerm_inXclass +  $training[$indexTraining][$term] + 1;      

                }
                $wordProbOnClassX[$term] = ($numberOfWord) / $sumTerm_inXclass;
                 
            }
            $prob[$indexClass]["wordProb"] =   $wordProbOnClassX;
            $prob[$indexClass]["classProb"] = $numberOfClass[$indexClass]/sizeof($training);
      

        }
        return $prob;
    }


    public function testing($termIndex,$wordProb,$classProb){       
        for ($classIndex=0; $classIndex < sizeof($classProb); $classIndex++) { 
            $results[$classIndex]["category"] = $classProb[$classIndex][0];
            $results[$classIndex]["prob"] = $classProb[$classIndex][1];
            for($i=0 ;$i < sizeof($termIndex);$i++){
                $results[$classIndex]["prob"] = $results[$classIndex]["prob"] * $wordProb[$classIndex][$termIndex[$i]];
            }

        }
        usort($results, function($a, $b) {
            return ($a['prob'] > $b['prob']) ? -1 : 1;
        });

        return $results[0]["category"];
    }

}
