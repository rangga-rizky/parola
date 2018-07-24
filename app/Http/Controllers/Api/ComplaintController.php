<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Tweet;
use App\Document;
use App\Category;
use App\Term;
use ForceUTF8\Encoding;
use App\TrainingTerm;
use App\Helper\LDATopicModels;
use App\Helper\CorrelationMeassure;
use App\Helper\Vectorizer;
use App\Helper\NaiveBayes;
use App\Helper\TextMiner;
use App\Helper\FileHandler;
use App\Transformers\ComplaintTransformer;
use App\Http\Requests\ComplaintRequest;

class ComplaintController extends Controller
{

	public function __construct(TextMiner $textMiner ,Tweet $tweet,ComplaintTransformer $complaintTransformer,FileHandler $fileHandler){
		$this->tweet = $tweet;
		$this->fileHandler = $fileHandler;
		$this->vectorizer = new Vectorizer($textMiner);
		$this->complaintTransformer = $complaintTransformer;
		$this->textMiner = $textMiner;
	}

    public function index(Request $request){
		$limit = $request->input('limit') ?: 35;
		$category = Category::find($request->input('category_id'));
        if(is_null($category)){
            $tweets = $this->tweet->orderBy("date","DESC")->paginate($limit);
        }else{            
    		$tweets = $this->tweet->where(["predicted" => $category->category])->orderBy("date","DESC")->paginate($limit);
        }  	
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


    public function categorize_cc(Request $request){  
		
    	ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 50000);  	
    	$correlationMeassure = new CorrelationMeassure();
		$complaints = $this->tweet->orderBy('id')->get();
		$categories = Category::all();	
    	$type  = $request->type;
        if(is_null($type)){            
			$terms = Term::orderBy('id')->pluck('term')->toArray(); 
			$path = "csv/1_training_assoc.csv";
        }else{
        	$terms = TrainingTerm::select('term', DB::raw('count(*) as total'))->orderBy("term")->groupBy("term")->pluck('term')->toArray(); 
            $path = "csv/1_automatic_assoc.csv";
		}
		$training = $this->fileHandler->readCSV($path,false)["file"];
		for ($i=0; $i < sizeof($complaints) ; $i++) { 			
			$binaryVector = $this->vectorizer->getBinaryVectorFromTokens(explode(",", $complaints[$i]->clean_tweet),$terms);
			$testing = $binaryVector["vector"];
			if(array_sum($testing) > 0){
				$predicted = $correlationMeassure->cosineSimilarity($training,$testing);
			}else{
				$predicted = "Tidak Terkategori";
			}
			$complaints[$i]->words = implode(", ", $binaryVector["words"]);
			$complaints[$i]->predicted = $predicted;
			$complaints[$i]->save();		
		}
		return response()->json([
		    'error' => False,
		    "message" => "data berhasil di kategorisasi",
		   // "top_bigrams" => $topBigrams
		]);
    }

    public function categorize_bayes(){
    	$naiveBayes = new NaiveBayes();
		$categories = Category::all();	    	
		$complaints = $this->tweet->orderBy('id')->get();		
		$training = array();
		$terms = array();
		$readWord = $this->fileHandler->readCSV("csv/1_word_prob.csv",false);
		$wordProb = $readWord["file"];
		$terms = $readWord["firstLine"];
		$classProb = $this->fileHandler->readCSV("csv/1_class_prob.csv",true)["file"];
		for ($i=0; $i < sizeof($complaints) ; $i++) { 
			$termIndex = array();
			$tokens = explode(",", $complaints[$i]->clean_tweet);
			for ($j=0; $j < sizeof($terms) - 1; $j++) { 
				if(in_array($terms[$j], $tokens)){
					$termIndex[] = $j;			
				}
			}
			if(empty($termIndex)){
				$predicted = "Tidak Terkategori";
			}else{
				$predicted = $naiveBayes->testing($termIndex,$wordProb,$classProb);	
			}			
			$complaints[$i]->predicted = $predicted;
			$complaints[$i]->save();

		}	
		return response()->json([
		    'error' => False,
		    "message" => "data berhasil di kategorisasi",
		   // "top_bigrams" => $topBigrams
		]);
	}
	
	public function topic_modelling(Request $request,$category_slug){
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 50000);
		$lda = new LDATopicModels();
		$n_of_topics = $request->k;
		$category = Category::where("slug",$category_slug)->first();
		if(empty($category)){
			return response()->json([
				'error' => True,
				"message" => "data tidak ditemukan",
			]);
		}
		$complaints =  $this->tweet->where("predicted",$category->category)->get();
		foreach ($complaints as $complaint) {			
			$tokenizedTokens[] = ["id" => $complaint->id,"tokens" => explode(",", $complaint->clean_tweet)];
		}
		$clustered_complaint = $lda->modelling($tokenizedTokens,$n_of_topics);
		for ($i=0; $i < $n_of_cluster ; $i++) { 			
			$terms = [];
			foreach ($clustered_complaint[$i]["words"] as $word => $value) {
				$terms[] = array($word,$value);
			}    
			$this->fileHandler->writeCSV('csv/'.$i.'_'.$category_slug.'_top-words.csv',$terms,null);	
			$complaints = $this->tweet->whereIn('id', $clustered_complaint[$i]["complaint_ids"])->update(['cluster' => $i]);
		}
		return response()->json([
		    'error' => False,
		    "message" => "Topik modelling berhasil",
		]);
		
	}
    
    public function uploadCSV(Request $request){
		if ($request->hasFile('file')) {				
			$correlationMeassure = new CorrelationMeassure();
			$path = 'files/';
        	$file_name = $this->fileHandler->upload($request->file('file'),$path);
        	if(is_null($file_name)){
        		return response()->json(['error' => true, 'message' => 'There is problem with upload file']);
			}
			$documents = $this->fileHandler->readCSV($path.$file_name,false)["file"];
			$distinct_terms = $this->fileHandler->readKeyPairCSV('csv/tweet_distinct_terms.csv');
			$fitur = TrainingTerm::select('term', DB::raw('count(*) as total'))->orderBy("term")->groupBy("term")->pluck('term')->toArray(); 
			$path = "csv/1_automatic_assoc.csv";
			$training = $this->fileHandler->readCSV($path,false)["file"];			
			foreach ($documents as $document) {				
				$utf8_string = Encoding::fixUTF8($document[1]);
				$words = $this->textMiner->textPreprocessing($this->textMiner->removeTweetAttr($utf8_string));
				$binaryVector = $this->vectorizer->getBinaryVectorFromTokens($words,$fitur);
				$testing = $binaryVector["vector"];
				if(array_sum($testing) > 0){
					$predicted = $correlationMeassure->cosineSimilarity($training,$testing);
				}else{
					$predicted = "Tidak Terkategori";
				}
				$this->tweet->create([
					'username' => '',
					'tweet' => $utf8_string,
					'predicted' => $predicted,
					'clean_tweet' => implode(",", $words),
					'timestamp' => 0,
					'date' => $document[0],
				]);
				foreach ($words as $word) {
					if (array_key_exists($word,$distinct_terms)){
					  $distinct_terms[$word] = intval($distinct_terms[$word]) + 1;
					}else{
					  $distinct_terms[$word] = 1;
					}    
				}            
			}
	
			foreach ($distinct_terms as $word => $value) {
				$terms[] = array($word,$value);
			}       
			$this->fileHandler->writeCSV('csv/tweet_distinct_terms.csv',$terms,null);
	
			return response()->json([
				"error" => False,
				"message" => sizeof($documents)." data berhasil masuk"
			]);
        }else{
			return response()->json([
				'error' => True,
				"message" => "Tidak ada file yang diupload",
			]);
		}
	}


	public function store(ComplaintRequest $request){
    	$correlationMeassure = new CorrelationMeassure();
        $distinct_terms = $this->fileHandler->readKeyPairCSV('csv/tweet_distinct_terms.csv');
		$words = $this->textMiner->textPreprocessing($this->textMiner->removeTweetAttr($request->complaint));
		$currentTime = date('Y-m-d H:i:s');
		foreach ($words as $word) {
			if (array_key_exists($word,$distinct_terms)){
			  $distinct_terms[$word] = intval($distinct_terms[$word]) + 1;
			}else{
			  $distinct_terms[$word] = 1;
			}    
		}  
		foreach ($distinct_terms as $word => $value) {
			$terms[] = array($word,$value);
		}       
		$this->fileHandler->writeCSV('csv/tweet_distinct_terms.csv',$terms,null);
		//$fitur = Term::orderBy('id')->pluck('term')->toArray(); 
		$fitur = TrainingTerm::select('term', DB::raw('count(*) as total'))->orderBy("term")->groupBy("term")->pluck('term')->toArray(); 
		$path = "csv/1_automatic_assoc.csv";
		$training = $this->fileHandler->readCSV($path,false)["file"];
		$binaryVector = $this->vectorizer->getBinaryVectorFromTokens($words,$fitur);
		$testing = $binaryVector["vector"];
		if(array_sum($testing) > 0){
			$predicted = $correlationMeassure->dotProduct($training,$testing);
		}else{
			$predicted = "Tidak Terkategori";
		}
        try{            
            $this->tweet->create([
					'username' => '',
					'tweet' => $request->complaint,
					'clean_tweet' => implode(",", $words),
					'words' => implode(",", $binaryVector["words"]),
					'timestamp' => 0,
					'date' => $currentTime,
					'predicted' => $predicted
				]);
        }catch(\Exception $e){
            return response()->json(['error' => true, 'message' => $e->getMessage()]);
        }

		return response()->json(['error' => false, 'message' => 'new data success created']);
			//$terms = TrainingTerm::select('term', DB::raw('count(*) as total'))->orderBy("id")->groupBy("term")->pluck('term')->toArray(); 
            //$path = "csv/1_automatic_assoc.csv";
    }
  

}
