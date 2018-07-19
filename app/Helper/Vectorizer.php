<?php

namespace App\Helper;
use App\Helper\TextMiner;

class Vectorizer
{

    public function __construct(TextMiner $textMiner){
        $this->textMiner = $textMiner;
    }

    public function getBinaryVector($complaint,$distinct_terms){
        $tokens = $this->textMiner->textPreprocessing($complaint);       
        $vector = array();
        $words = array();
        for ($j=0; $j < sizeof($distinct_terms); $j++) { 
            if(in_array($distinct_terms[$j], $tokens)){
                $vector[$j] = 1;
                $words[] = $distinct_terms[$j];
            }else{
                $vector[$j] = 0;
            }
        }

        return [
                    "words" => $words,
                    "vector" => $vector
                ];
    }

    public function getBinaryVectorFromTokens($tokens,$distinct_terms){
        $vector = array();
        $words = array();
        for ($j=0; $j < sizeof($distinct_terms); $j++) { 
            if(in_array($distinct_terms[$j], $tokens)){
                $vector[$j] = 1;
                $words[] = $distinct_terms[$j];
            }else{
                $vector[$j] = 0;
            }
        }

        return [
                    "words" => $words,
                    "vector" => $vector
                ];
    }



   /* public function hitungTFIDFLabeled($complaints,$terms,$labeled_category){

    	for ($i=0; $i < sizeof($terms); $i++) { 
    		$df = 0;
    		$results[$i]["term"] = $terms[$i];
    		for ($j=0; $j < sizeof($complaints) ; $j++) { 
    			$frekuensi = 0;
    			$tokens = explode(" ", $complaints[$j]);
    			foreach ($tokens as $token) {
    				if($token == $terms[$i]){
    					$frekuensi++;
    				}
    			}
    			if($frekuensi > 0){
    				$df++;
    			}
    			$results[$i]["tf-d".$j] = $frekuensi;
    		}
    		$results[$i]["idf"] = log10(sizeof($complaints)/$df);
    	}

    	for ($i=0; $i < sizeof($complaints); $i++) { 
    		$data[$i]["0"] = $complaints[$i];
    		for ($j=0; $j < sizeof($results) ; $j++) { 
              $data[$i][$results[$j]["term"]] = $results[$j]["tf-d".$i] * ($results[$j]["idf"]+1);
          }
          
    		$data[$i]["labeled_category"] = $labeled_category[$i];
      }

      return $data;

  }

   public function hitungTFIDF($complaints,$terms){

    	for ($i=0; $i < sizeof($terms); $i++) { 
    		$df = 0;
    		$results[$i]["term"] = $terms[$i];
    		for ($j=0; $j < sizeof($complaints) ; $j++) { 
    			$frekuensi = 0;
    			$tokens = explode(" ", $complaints[$j]);
    			foreach ($tokens as $token) {
    				if($token == $terms[$i]){
    					$frekuensi++;
    				}
    			}
    			if($frekuensi > 0){
    				$df++;
    			}
    			$results[$i]["tf-d".$j] = $frekuensi;
    		}
    		$results[$i]["idf"] = log10(sizeof($complaints)/$df);
    	}

    	for ($i=0; $i < sizeof($complaints); $i++) { 
    		$data[$i]["complaints"] = $complaints[$i];
    		for ($j=0; $j < sizeof($results) ; $j++) { 
              $data[$i][$results[$j]["term"]] = $results[$j]["tf-d".$i] * ($results[$j]["idf"]+1);
          }
      }

      return $data;

  }

  public function hitungTopWord($documents,$terms,$c){

    foreach ($terms as $term) {
        $total = 0;
        for ($i=0; $i < sizeof($documents); $i++) { 
            $total = $total + $documents[$i][$term];
        }
        $result["term"] = $term;
        $result["category_id"] = $c;
        $result["score"] = $total;
        $results[] = $result;

    }


    usort($results, function($a, $b) {
        return ($a['score'] > $b['score']) ? -1 : 1;
    });

    return $results;

	}	*/



    /*private function entropy($data, $target_attr){
        $val_freq = array();
        $data_entropy = 0.0;

        foreach ($data as $record) {

            if(array_key_exists($record[$target_attr] , $val_freq)){
                $val_freq[$record[$target_attr]]++; 
            }else{
                $val_freq[$record[$target_attr]] = 1.0;
            }   
        }

        foreach ($val_freq as $freq) {
            $data_entropy = $data_entropy + (-1 * ($freq/sizeof($data))) * log($freq/sizeof($data),2);
        }

        return $data_entropy;
    }

    public function gain($data,$attr, $target_attr){
        $val_freq = array();
        $subset_entropy = 0.0;

        foreach ($data as $record) {            
            /*if($record[$attr] > 0){
                $record[$attr] = 1;
            }else{
                $record[$attr] = 0;
            }
            if(array_key_exists($record[$attr] , $val_freq)){
                $val_freq[$record[$attr]]++; 
            }else{
                $val_freq[$record[$attr]] = 1.0;
            }   
        }

        foreach ($val_freq as $key => $value) {
            $val_prob = $val_freq[$key] / array_sum($val_freq);
            $data_subset = array();
            foreach ($data as $record) {
                if($record[$attr] == $key){
                    $data_subset[] = $record;
                }
            }
            $subset_entropy =  $subset_entropy + $val_prob * $this->entropy($data_subset, $target_attr);
        }
        return ($this->entropy($data, $target_attr) - $subset_entropy);
    }*/

}
