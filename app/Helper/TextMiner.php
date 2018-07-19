<?php

namespace App\Helper;
use SastrawiStemmer;


class TextMiner
{

    private $distinct_terms = [];

    function __construct(){
        $stemmerFactory = new SastrawiStemmer();
        $this->stemmer  = $stemmerFactory->createStemmer();

    }

	public function textPreprocessing($text){  
        $stopwords = $this->loadStopwords();        
        $output = $this->removeNUmber($text);            
        $output = $this->stemmer->stem($output);
        $data[] = $output;
        $tokens = explode(" ",$output);
        $filteredToken = array();
        foreach ($tokens as $token) {   
            if ((strlen($token) > 2) && !in_array(trim($token), $stopwords))  {                
                $filteredToken[] = $token;
                if(!in_array($token, $this->distinct_terms)){
                    $this->distinct_terms[] = $token;
                }
            }
        }
        return $filteredToken;
    }

    public function removeTweetAttr($tweet){
        return preg_replace(array('(@[A-Za-z0-9]+)','(\w+:\/\/\S+)'),'', $tweet);
    }


    public function getDistinctTerm(){
        return $this->distinct_terms;
    }

    private function loadStopwords(){
    	$stopwords = [];    
        $handle = fopen("stopword_list.txt", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {            
                $stopwords[] = trim($line); 
            }
            fclose($handle);
        }
        return $stopwords;

    }

    private function removeNUmber($text){
    	return preg_replace('/[0-9]+/', '', $text);
    }

    /*public function buildBigrams($complaints){
        $distinct_terms = array();  
        $tokenize_data = array();     
        $stopwords = $this->loadStopwords();

        foreach ($complaints as $complaint) {

            $sentence = $complaint["complaint"];             
            $output = $this->removeNUmber($sentence);            
            $output = $this->stemmer->stem($output);
            $data[] = $output;
            $labeled_category[] =  $complaint["category"];

            $tokens = explode(" ",$output);
            $filteredToken = array();
            foreach ($tokens as $token) {
                if(!in_array($token, $stopwords)){
                    $filteredToken[] = $token;
                }
            }
            $filteredToken = $this->bigrams($filteredToken);
            foreach ($filteredToken as $token) {
                if(!in_array($token, $distinct_terms)){
                    $distinct_terms[] = $token;
                }
            }
            $tokenize_data[] = $filteredToken;
        }
        return [
            "distinct_terms" => $distinct_terms,
            "data" => $data,
            "tokenize_data" => $tokenize_data,
            "labeled_category" => $labeled_category
        ];
    }

    public function bigrams($words){
        $ngrams = array();
        $len = sizeof($words);
        for($i=0;$i+1<$len;$i++){
            if(strlen($words[$i]) > 2 && strlen($words[$i+1]) > 2){                
                $ngrams[]=$words[$i]."_".$words[$i+1];
            }
       }
        return $ngrams;
    }*/

}