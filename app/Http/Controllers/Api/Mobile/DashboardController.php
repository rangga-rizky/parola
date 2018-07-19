<?php

namespace App\Http\Controllers\Api\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tweet;
use App\User;
use App\Category;
use DB;

class DashboardController extends Controller
{
    public function __construct(Tweet $tweet){
		$this->tweet = $tweet;
    }
    
    public function dashboard(){
        $user = $this->getUser();
        $n_data = $this->tweet->count();
        $n_category = Category::all()->count();
        $complaint_freq = $this->getComplaintFreq(null);
        $most_categorized = DB::select("CALL get_most_frequent_category()");
        $distCategory = $this->getDistCategory(null,null);
        return response()->json([
            
            "last_login" => $user->last_login,
            "user_name" => $user->name,
            "n_data" => $n_data,
            "n_category" => $n_category,
            "most_categorized" => $most_categorized,
            "complaint_freq" => $complaint_freq,
            "distCategory" => $distCategory
        ]);         
    }

    private function getDistCategory($month,$year){
        if(!is_null($month) && !is_null($year)){
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
        return $results;
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
}
