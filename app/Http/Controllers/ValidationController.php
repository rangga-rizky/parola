<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Document;
use App\Category;
use App\Term;
use App\Helper\TextMiner;
use App\Helper\CorrelationMeassure;
use App\Helper\Vectorizer;
use App\Helper\FileHandler;
use App\Helper\NaiveBayes;
use App\CsvFile;
use DB;

class ValidationController extends Controller
{
    private $size_of_docs = 2468;

    public function loo(){
    	ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 50000); 
        $missclassified = 0;
    	$corr = new CorrelationMeassure();
    	$vectorizer = new Vectorizer(new TextMiner);
        $fileHandler = new FileHandler();
    	$categories = Category::all();

        $distinct_terms = $fileHandler->readCSV("csv/distinct_terms.csv",true)["file"][0];
        
        $terms = [];
        $termsperCategory = [];
        for($c = 0; $c < sizeof($categories); $c++){ 
            //$testing_token =  explode(",", trim($test_data->clean_complaint));
            
            $file1 = fopen("csv/terms_per_c/".$categories[$c]->id.".csv", 'r');
            while (($line = fgetcsv($file1)) !== FALSE) {
                $topWords[$line[0]] = intval($line[1]);                
            }              
            fclose($file1);
          //  foreach ($testing_token as $word) {
            //    $topWords[$word]--;                    
           // }
           //
            $nonZeroWods = array_filter($topWords, function($val) { return $val > 0; });            
            $thresHolded = array_filter($topWords, function($val) use ($nonZeroWods) { return $val > (array_sum($nonZeroWods)/sizeof($nonZeroWods)); });
            foreach ($thresHolded as $topword => $value) {
                $termsperCategory[$categories[$c]->category][] = ["word" => $topword,"score"=>$value];
                if(!in_array($topword, $terms)){
                    $terms[] = $topword;
                }                    
            }                
        }

    	for ($i=1; $i <= 2468 ; $i++) { 
    		$test_data = Document::find($i);
    		
            $matriks = $this->buildMatrice($terms,$termsperCategory,$categories);  
            $training = array_slice($matriks, 1);
        	$binaryVector = $vectorizer->getBinaryVector($test_data->complaint,$terms);  
			$testing = $binaryVector["vector"];
            $predicted = $corr->dotProduct($training,$testing);
           // dd($predicted);
            $test_data->manual_words = implode(", ", $binaryVector["words"]);
            $test_data->manual_predicted = $predicted;
            $test_data->save(); 
			if($test_data->category != $predicted){
				$missclassified++;
            }
          //  dd($predicted);
    	}
    	dd($missclassified);
    }

    private function buildMatrice($distinct_terms,$termsperCategory,$categories){
       /*$fitur[] = "";
        foreach ($distinct_terms as $term) {
           $fitur[] = $term ;
        }
        $matriks[] = $fitur;

        foreach ($categories as $category) {            
            $terms = $termsperCategory[$category->category];
            $association_word[0] = $category->category;
            $sumScore = array_sum(array_column($terms, 'score'));
            for ($i=1; $i < sizeof($fitur) ; $i++) { 
                for ($j=0; $j < sizeof($terms); $j++) { 
                    if($terms[$j]["word"] == $fitur[$i]){
                        $association_word[$i] = $terms[$j]["score"]/$sumScore;
                        break;
                    }else{
                        $association_word[$i] = 0;
                    }   
                }              
            }//dd($association_word);
            $matriks[] = $association_word;
        } 
        */

        $fitur[] = "";
        foreach ($distinct_terms as $term) {
           $fitur[] = $term ;
        }
        $matriks[] = $fitur;
        foreach ($categories as $category) {            
            $terms = $termsperCategory[$category->category];
            $association_word[0] = $category->category;
            for ($i=0; $i < sizeof($distinct_terms) ; $i++) { 
                for ($j=0; $j < sizeof($terms); $j++) { 
                    if($terms[$j]["word"] == $distinct_terms[$i]){
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


    public function score(){

        $categories = Category::all();
        foreach ($categories as $category) {
            $n = Document::where("category_id",$category->id)->count();
            $benar = Document::where("predicted",$category->category)->where("category_id",$category->id)->count();          
            echo $category->category." : ".($benar)/$n * 100;
            echo "<br>";
        }
        echo "<br>";
        $documents = Document::all();
        $benar = DB::table('documents')
                ->whereRaw('predicted = category')
                ->count();
        echo "Jumlah data = ".sizeof($documents)." Akurasi : ".($benar/sizeof($documents) * 100);

        
    }


    public function clean_data(){
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 50000);
        $fileHandler = new FileHandler(); 
        $textMiner = new TextMiner();   
        $documents = Document::orderBy("id")->get();
        $categories = Category::all();
        $terms = [];

        foreach ($documents as $document) {            
            $clean_text[] = $textMiner->textPreprocessing($document->complaint);
        }  
        $terms = $textMiner->getDistinctTerm();

        $fp = fopen('csv/distinct_terms.csv', 'w');
        fputcsv($fp, $terms);
        fclose($fp);  

        for ($i=0; $i < sizeof($documents); $i++) { 
            $documents[$i]->clean_complaint = implode(",", $clean_text[$i]);
            $documents[$i]->save();
        }

        $distinct_terms = $fileHandler->readCSV("csv/distinct_terms.csv",true)["file"][0];

        for($c = 0; $c < sizeof($categories); $c++){    
            $topWords = array();        
            $complaints = Document::orderBy("id")->where("category",$categories[$c]->category)->get(); 
            $trainings = [];
            foreach ($complaints as $complaint) {
                $trainings[] = explode(",", $complaint->clean_complaint);
            }  

            foreach ($distinct_terms as $distinct_term) {
                $topWords[$distinct_term] = 0;
            }   

            foreach ($trainings as $training) {
                for ($i=0; $i < sizeof($distinct_terms); $i++) { 
                    if(in_array($distinct_terms[$i], $training)){
                        $topWords[$distinct_terms[$i]]++;
                    }
                }
            }        
            
            $fs = fopen('csv/terms_per_c/'.$categories[$c]->id.'.csv', 'w');
            foreach ($topWords as $key => $value) {
                 fputcsv($fs,array($key,$value));
            }
            fclose($fs);  
        }

    }

     public function dist(){
        $categories = Category::all();
        foreach ($categories as $category) {
            $n = Document::where("category_id",$category->id)->count();          
            echo $category->category." : ".$n ;
            echo "<br>";
        }
        $total_data = Document::count();
        echo $total_data." data";
    }
   
    public function binaryMatrix(){
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 50000);

        $vectorizer = new Vectorizer(new TextMiner());
        $fileHandler = new FileHandler(); 
       
        $distinct_terms = $fileHandler->readCSV("csv/distinct_terms.csv",true)["file"][0];
        $complaints = Document::orderBy("id")->get(); 
        $trainings = [];
        foreach ($complaints as $complaint) {
            $trainings[] = explode(",", $complaint->clean_complaint);
        }  

        foreach ($distinct_terms as $distinct_term) {
            $topWords[$distinct_term] = 0;
        }           
        foreach ($trainings as $training) {
            for ($i=0; $i < sizeof($distinct_terms); $i++) { 
                if(in_array($distinct_terms[$i], $training)){
                    $topWords[$distinct_terms[$i]]++;
                }
            }
        }
        
        
        //arsort($topWords);
        //$topWords = array_slice( $topWords, 0,350); 
        $nonZeroWods = array_filter($topWords, function($val) { return $val > 0; });
        $thresHolded = array_filter($topWords, function($val) use ($nonZeroWods) { return $val > array_sum($nonZeroWods)/sizeof($nonZeroWods); });   
        arsort($thresHolded);
       // dd(sizeof($thresHolded));
        foreach ($thresHolded as $word => $value) {
            $terms[] =  $word;
        }

        for ($c=0; $c < sizeof($complaints) ; $c++) { 
            $binaryVector = array();
            $result = $vectorizer->getBinaryVector($complaints[$c],$terms);
            for ($i=0; $i < sizeof($terms); $i++) { 
                $binaryVector[$terms[$i]] = $result["vector"][$i];
            }
            $binaryVector["labeled_category"] = $complaints[$c]->category;
            $binaryMatrix[] = $binaryVector;
        }

        $fp = fopen('csv/validation/binary_matrix.csv', 'w');
        fputcsv($fp, $terms);
        for ($i=0; $i < sizeof($binaryMatrix) ; $i++) { 
            fputcsv($fp, $binaryMatrix[$i]);
        }

        fclose($fp);


    }

    public function bayes(){    

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 50000);    
        $fileHandler = new FileHandler(); 
        $naivBayes = new NaiveBayes();
        $trainingCSV = $fileHandler->readCSV("csv/validation/binary_matrix.csv",false);
        $dataCSV = $trainingCSV["file"];
        $terms = $trainingCSV["firstLine"];
        $complaints = Document::orderBy("id")->get(); 
        $category_name = Category::orderBy("id")->pluck('category')->toArray();
        $datasets = [];
        for ($i=0; $i < sizeof($dataCSV)  ; $i++) { 
            for ($j=0; $j < sizeof($terms) ; $j++) { 
                $datasets[$i][$terms[$j]] = $dataCSV[$i][$j];
            }
            $datasets[$i]["labeled_category"] = $dataCSV[$i][$j];
        }

        
        for ($i=1500; $i < 2468 ; $i++) { 
            $wordProb = [];
            $classProb = [];
            $testing = $datasets[$i];
            $training = $datasets;
            array_splice($training, $i,1);            
            $probs = $naivBayes->training($training,$category_name,$terms);
           // dd($);
            for ($j=0; $j < sizeof($probs); $j++) { 
                $wordProb[$j] = $probs[$j]["wordProb"];
                $classProb[$j] = $arrayName = array(0 => $category_name[$j],1 => $probs[$j]["classProb"]); 
            }

            $termIndex = [];
            for ($j=0; $j < sizeof($terms); $j++) { 
                if($testing[$terms[$j]] == 1){
                    $termIndex[] = $terms[$j];
                }
            }
           // dd($naivBayes->testing($termIndex,$wordProb,$classProb));
            $document = Document::find($i+1);
            $document->bayes_predicted = $naivBayes->testing($termIndex,$wordProb,$classProb);
            $document->save();
        }
    }

    public function manual(){
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 50000);   
        $missclassified = 0;
        $path = "csv/1_training_assoc.csv";
        $corr = new CorrelationMeassure();
        $vectorizer = new Vectorizer(new TextMiner);
        $fileHandler = new FileHandler();
        $categories = Category::all();

        $terms = Term::orderBy('id')->pluck('term')->toArray();

        for ($i=1; $i <= $this->size_of_docs; $i++) {             
            $test_data = Document::find($i);
            $training = $fileHandler->readCSV($path,false)["file"];
            $binaryVector = $vectorizer->getBinaryVector($test_data->complaint,$terms);  
            $testing = $binaryVector["vector"];           
            $predicted = $corr->dotProduct($training,$testing);
            $test_data->manual_words = implode(", ",$binaryVector["words"]);
            $test_data->manual_predicted = $predicted;
            $test_data->save(); 
            if($test_data->category != $predicted){
                $missclassified++;
            }
        }
        dd($missclassified);
    }

    
}

