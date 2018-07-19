<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Document;
use App\Category;
use App\TrainingTerm;
use App\Helper\Vectorizer;
use App\Helper\FileHandler;
use App\Helper\TextMiner;
use App\Helper\NaiveBayes;
use App\Helper\KeywordExtractor;
use App\Transformers\TrainingTransformer;
use DB;

class TrainingController extends Controller
{
    
    public function __construct(Document $document,TrainingTransformer $trainingTransformer,FileHandler $fileHandler){
    	$this->document = $document;
        $this->fileHandler = $fileHandler;
    	$this->trainingTransformer = $trainingTransformer;
    }

    public function index(Request $request){
        $limit = $request->input('limit') ?: 35;
        $category_id  = $request->input('category_id');
        if(is_null($category_id)){            
            $documents = $this->document->paginate($limit);
        }else{            
            $documents = $this->document->where('category_id',$category_id)->paginate($limit);
        }
		return  $this->respondWithPagination($documents, [
            'data' =>  $this->trainingTransformer->transformCollection($documents)
        ]);
    }

    public function cleanData(){
        $textMiner = new TextMiner();   
        $documents = $this->document->orderBy("id")->get();
        $distinct_terms = [];

        foreach ($documents as $document) {    
            $document->clean_complaint = implode(",", $textMiner->textPreprocessing($document->complaint));
            $document->save();
        }  
        $distinct_terms = $textMiner->getDistinctTerm();
        $this->fileHandler->writeCSV('csv/distinct_terms.csv',array($distinct_terms),null);
        return response()->json([
            "error" => false,
            "message" => "data successfully preprocessed"
        ]);

    }

    public function binaryMatrice(){        
        $vectorizer = new Vectorizer(new TextMiner());       
        $distinct_terms = $this->fileHandler->readCSV("csv/distinct_terms.csv",true)["file"][0];
        $complaints = $this->document->orderBy("id")->get(); 
        
        foreach ($distinct_terms as $distinct_term) {
            $topWords[$distinct_term] = 0;
        }           
        foreach ($complaints as $complaint) {
            $training = explode(",", $complaint->clean_complaint);
            for ($i=0; $i < sizeof($distinct_terms); $i++) { 
                if(in_array($distinct_terms[$i], $training)){
                    $topWords[$distinct_terms[$i]]++;
                }
            }
        }
        arsort($topWords);
        $topWords = array_slice( $topWords, 0,350); 
        foreach ($topWords as $word => $value) {
            $terms[] =  $word;
        }
        for ($c=0; $c < sizeof($complaints) ; $c++) { 
            $binaryVector = array();
            $result = $vectorizer->getBinaryVectorFromTokens(explode(",", $complaints[$c]->clean_complaint),$terms);
            for ($i=0; $i < sizeof($terms); $i++) { 
                $binaryVector[$terms[$i]] = $result["vector"][$i];
            }
            $binaryVector["labeled_category"] = $complaints[$c]->category;
            $binaryMatrix[] = $binaryVector;
        }
        $this->fileHandler->writeCSV('csv/binary_matrix.csv',$binaryMatrix,$terms);
        return response()->json([
            "error" => false,
            "message" => "binary matrice successfully created"
        ]);
    }

    public function trainBayes(Request $request){
        $naiveBayes = new NaiveBayes();
        $complaints = $this->document->orderBy("id")->get();                                           #->limit(1700)
        $category_name = Category::orderBy("id")->pluck('category')->toArray();
        $trainingCSV = $this->fileHandler->readCSV("csv/binary_matrix.csv",false);
        $dataCSV = $trainingCSV["file"];
        $terms = $trainingCSV["firstLine"];          
        for ($i=0; $i < sizeof($dataCSV)  ; $i++) { 
            for ($j=0; $j < sizeof($terms) ; $j++) { 
                $binaryMatrix[$i][$terms[$j]] = $dataCSV[$i][$j];
            }
            $binaryMatrix[$i]["labeled_category"] = $dataCSV[$i][$j];
        }
        $prob = $naiveBayes->training($binaryMatrix,$category_name,$terms);
        $fp = fopen('csv/'.$request->project_id.'_class_prob.csv', 'w');
        $fpWord = fopen('csv/'.$request->project_id.'_word_prob.csv', 'w');
        fputcsv($fpWord, $terms);
        for ($i=0; $i < sizeof($prob) ; $i++) { 
            fputcsv($fp, array($category_name[$i],$prob[$i]["classProb"]));
            fputcsv($fpWord, $prob[$i]["wordProb"]);
        }
        fclose($fp);
        fclose($fpWord);

        return  response()->json([
            'status' => 'success',
            'message' => 'Data successfully trained',
        ]);
    }
    

    public function extractKeyword(Request $request){
        TrainingTerm::query()->truncate();
        $categories = Category::all();
        $keywordExtractor = new KeywordExtractor();
        $distinct_terms = $this->fileHandler->readCSV("csv/distinct_terms.csv",true)["file"][0];
        for($c = 0; $c < sizeof($categories); $c++){ 
            $tokenized_text = [];           
            $complaints = $this->document->orderBy("id")->where("category_id",$categories[$c]->id)->get(); 
            foreach ($complaints as $complaint) {
                $tokenized_text[] = explode(",", $complaint->clean_complaint );            
            }
            $topWords = $keywordExtractor->aggregate($tokenized_text,$distinct_terms);
            $i = 0;
            $trainingTerm = array();
            foreach ($topWords as $key => $value) {
                $trainingTerm[$i]["term"] = $key;
                $trainingTerm[$i]["category_id"] = $categories[$c]->id;
                $trainingTerm[$i]["score"] = $value;
                $trainingTerm[$i]["project_id"] = $request->project_id;
                $i++;
            }
            TrainingTerm::insert($trainingTerm);
        }

        return  response()->json([
            'status' => 'success',
            'message' => 'Keyword successfully extracted',
        ]);
    }


    public function showScore($method){             
        $nDocuments = Document::count();        
        $categories = Category::all();  
        switch ($method) {
            case 'bayes':
                $scores = $this->calculateScore("bayes_predicted",  $nDocuments, $categories);
                break;
            
            case 'manual':
                $scores = $this->calculateScore("manual_predicted",  $nDocuments, $categories);
                break;

            case 'auto':
                $scores = $this->calculateScore("predicted",  $nDocuments, $categories);
                break;
            
            default:
                $scores["categories"] = $categories->pluck("category")->toArray();
                $scores["bayes"] = $this->calculateScore("bayes_predicted",  $nDocuments, $categories);
                $scores["manual"] = $this->calculateScore("manual_predicted",  $nDocuments, $categories);
                $scores["auto"] = $this->calculateScore("predicted",  $nDocuments, $categories);
                break;
        }
        $scores["number_of_data"] = $nDocuments;
        return response()->json($scores);
    }

    private function calculateScore($predicted_column,$nDocuments,$categories){            
    	$scores = [];    	     
        foreach ($categories as $category) {
        	$n = $this->document->where("category_id",$category->id)->count();
            $n_predicted = $this->document->where($predicted_column,$category->category)->count();
            $true_pred = $this->document->where($predicted_column,$category->category)
							            ->where("category_id",$category->id)
							            ->count();      
            $score["precission"] = ($true_pred)/$n_predicted * 100; 
            $score["recall"] = ($true_pred)/$n * 100;   
            $score["number_of_data"] = $n;         
        	$score["category"] = $category->category;
        	$scores[] = $score;
        }
        $true_pred = DB::table('documents')
                ->whereRaw($predicted_column.' = category')
                ->count();
        $accuracy = ($true_pred/($nDocuments) * 100);
        return [
        	"scores" => $scores,
        	"accuracy" => $accuracy,
        ];
    }
}

/*

labeled kesehatan
200 kesehatan
2300 bukan kesehatan

prediksi
150 kesehatan
dari 150 prediksi
135 benar
15 salah

precisi 135/150
recall 135/200
accuracy */