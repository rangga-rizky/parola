<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tweet;
use App\Term;
use App\Category;
use App\TrainingTerm;
use App\Helper\FileHandler;
use App\Helper\KeywordExtractor;
use DB;

class DashboardController extends Controller
{
    public function __construct(Tweet $tweet,FileHandler $fileHandler){
		$this->tweet = $tweet;
        $this->fileHandler = $fileHandler;
    }
    
    public function dashboard(){
        $last_login = date('Y-m-d H:i:s'); // $this->getUser()->last_login;
       // $n_data = $this->tweet->count();
        $n_category = Category::all()->count();
        $periode_options = DB::select("CALL get_available_periode()");
        return response()->json([
            "last_login" => $last_login,
            //"n_data" => $n_data,
            "n_category" => $n_category,
            "periode_options" =>$periode_options
        ]);  
    }

	public function getChartData(Request $request){ 
        switch($request->data){
            case "freq-data": $result = response()->json($this->getComplaintFreq(null));
                              break;
            case "top-words": $result = $this->getTopWords(null,null);
                              break;
            case "dist-category-line": $result = $this->getDistCategoryLineData($request->month,$request->year);
                              break;
            case "dist-category": $result = $this->getDistCategory($request->month,$request->year);
                              break;
            default: $result = null; break;
        }        
        return $result;             
    }
        
    
    public function get_dashboard_category($category_slug,$mode){        
        $category = Category::where('slug',$category_slug)->first();
        switch($mode){
            case "frekuensi": $result = response()->json($this->getComplaintFreq($category->category));
                              break;
            case "top-words": $result = $this->get_category_topwords($category);
                              break;
            case "all": $result = response()->json([
                                    "frekuensi" => $this->getComplaintFreq($category->category),
                                    "top-words" =>  $this->get_category_topwords($category)
                                ]);
                              break;
            default:$result = $category->category; break;
        }        
        return $result;
        
    }

    private function getTopWords($month,$year){
        if(!is_null($month) && !is_null($year)){
            return $this->getTopwordByMonth($month,$year);
        }

        $distinct_terms = $this->fileHandler->readKeyPairCSV("csv/tweet_distinct_terms.csv",false);
        arsort($distinct_terms);
        $topWords = array_slice( $distinct_terms, 0,10); 

        foreach ($topWords as $word => $value) {
           $words[] = $word;
           $values[] = intval($value);
        }
        return response()->json( [
                    "labels"=> $words,
                    "data" => $values
                ]);
    }

    private function getTopwordByMonth($month,$year){
        $keywordExtractor = new KeywordExtractor;       
        $complaints = $this->tweet->whereYear('date', '=', $year)->whereMonth('date', '=', $month)->pluck('clean_tweet')->toArray();
        $distinct_terms = $this->fileHandler->readKeyPairCSV("csv/tweet_distinct_terms.csv",false);
        $distinct_terms = array_keys($distinct_terms);

        foreach ($complaints as $complaint) {
            $tokenized_text[] = explode(",", $complaint);
        }
        
        $topWords = $keywordExtractor->aggregate_n($tokenized_text,$distinct_terms,10);
        foreach ($topWords as $word => $value) {
            $words[] = $word;
            $values[] = intval($value);
        }
        return response()->json(  [
            "labels"=> $words,
            "data" => $values
        ]);
    }

    private function getDistCategory($month,$year){
        if(!is_null( $month) && !is_null($year)){
            $number_of_predicted = DB::select("CALL get_number_of_predicted_by_month('$month','$year')");
        }else{            
            $number_of_predicted = DB::select("CALL get_number_of_predicted()");
        }
        foreach ($number_of_predicted as  $data) {
            $results[] = [
                            "name" => $data->predicted,
                            "y" => $data->jumlah
                        ];
        }
        return response()->json($results);
    }

    private function getDistCategoryLineData($month,$year){
        if(!is_null( $month) && !is_null($year)){
            $number_of_predicted = DB::select("CALL get_number_of_predicted_by_month('$month','$year')");
        }else{            
            $number_of_predicted = DB::select("CALL get_number_of_predicted()");
        }
        foreach ($number_of_predicted as  $data) {
            $labels[] = $data->predicted;
            $values[] = $data->jumlah;         
        }
        return [
            "labels"=> $labels,
            "data" => $values
        ];
    }

    private function getComplaintFreq($category){
        if(is_null($category)){
            $n_of_data = DB::select("CALL get_complaint_freq()");
        }else{
            $q = 
            $n_of_data = DB::select("CALL get_complaint_freq_by_category('$category')");
        }      
        foreach ($n_of_data as $data) {
           $labels[] = $data->date;
           $values[] = $data->count;
        }

        return [
                    "labels"=> $labels,
                    "values" => $values
                ];
    }



    private function get_category_topwords($category){
        $keywordExtractor = new KeywordExtractor;       
        $complaints = $this->tweet->where('predicted',$category->category)->pluck('clean_tweet')->toArray();
        $distinct_terms = $this->fileHandler->readKeyPairCSV("csv/tweet_distinct_terms.csv",false);
        $distinct_terms = array_keys($distinct_terms);

        foreach ($complaints as $complaint) {
            $tokenized_text[] = explode(",", $complaint);
        }
        
        $topWords = $keywordExtractor->aggregate_n($tokenized_text,$distinct_terms,40);
        foreach ($topWords as $word => $value) {
            $result[] = array('name' => $word, 
                                'weight' => $value
                                ); 
        }
        return $result;
    }

    public function getClusteredTopWords($category_slug){
        for ($i=0; $i < 4 ; $i++) {        
            $clustered_words = [];   
            $words = $this->fileHandler->readKeyPairCSV('csv/'.$i.'_'.$category_slug.'_top-words.csv');
            foreach ($words as $word => $value) {
                $clustered_words[] = array('name' => $word, 
                                    'weight' => $value
                                    ); 
            }   
            $results[] = $clustered_words;
        }            
        return response()->json($results);
    }

    



}
