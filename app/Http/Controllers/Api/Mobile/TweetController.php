<?php

namespace App\Http\Controllers\Api\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\ComplaintTransformer;
use App\Tweet;

class TweetController extends Controller
{
    public function __construct(Tweet $tweet,ComplaintTransformer $complaintTransformer){
		$this->tweet = $tweet;
		$this->complaintTransformer = $complaintTransformer;
    }
    
    public function index(Request $request){
    	$limit = $request->input('limit') ?: 35;
    	$tweets = $this->tweet->orderBy("date","DESC")->paginate($limit);    	
    	$n = $this->tweet->count();
    	$latest_data = $this->tweet->orderBy("date","DESC")->first()->getDateTimeLocalized();
    	return  $this->respondWithPagination($tweets, [
            'data' =>  $this->complaintTransformer->transformCollection($tweets),
            'meta' => array('number_of_data' => $n, 'latest_data' => $latest_data )
        ]);
    }

    public function indexByPredicted($category_slug,Request $request){
    	$category = Category::where('slug',$category_slug)->first();
		$limit = $request->input('limit') ?: 35;
		if(is_null($request->input('cluster'))){			
			$tweets = $this->tweet->where('predicted',$category->category)->orderBy("date","DESC")->paginate($limit); 			
			$n = $this->tweet->where('predicted',$category->category)->count();
		}else{			
			$tweets = $this->tweet->where('predicted',$category->category)
								   ->where('cluster',$request->input('cluster'))
								   ->orderBy("date","DESC")
								   ->paginate($limit); 
			$n = $this->tweet->where('predicted',$category->category)
							->where('cluster',$request->input('cluster'))
							->count();
		}   	
    	$latest_data = $this->tweet->where('predicted',$category->category)->orderBy("date","DESC")->first()->getDateTimeLocalized();
    	return  $this->respondWithPagination($tweets, [
    		'title' => $category->category,
            'data' =>  $this->complaintTransformer->transformCollection($tweets),
            'meta' => array('number_of_data' => $n, 'latest_data' => $latest_data )
        ]);
    }
}
