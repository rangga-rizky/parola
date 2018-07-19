<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TrainingTerm;
use App\Category;
use App\Helper\CorrelationMeassure;
use App\Helper\FileHandler;
use DB;
use App\Http\Requests\TrainingTermRequest;
use App\Transformers\TermTransformer;

class TrainingTermController extends Controller
{
    public function __construct(TrainingTerm $trainingTerm,TermTransformer $termTransformer){
		$this->trainingTerm = $trainingTerm;
		$this->termTransformer = $termTransformer;
	}
    public function index(Request $request){
        $user = $this->getUser();
        $limit = $request->input('limit') ?: 15;
        $category_id  = $request->input('category_id');
        if(is_null($category_id)){
            $trainingTerms = $this->trainingTerm->where('project_id',$user->project->id)->paginate($limit);
        }else{
            $trainingTerms = $this->trainingTerm->where([
                "category_id" => $category_id,
                'project_id' => $user->project->id
            ])->paginate($limit);
        }

		return  $this->respondWithPagination($trainingTerms, [
            'data' =>  $this->termTransformer->transformCollection($trainingTerms)
        ]);
    }

    public function search(Request $request,$q){
        $limit = $request->input('limit') ?: 15;
        $trainingTerms = $this->trainingTerm->where('term', 'like', '%' . $q . '%')->paginate($limit);
        return  $this->respondWithPagination($trainingTerms, [
            'data' =>  $this->termTransformer->transformCollection($trainingTerms)
        ]);
    }   

     public function generateTermAssoc(Request $request){
        $corr = new CorrelationMeassure();
        $fileHandler = new FileHandler();
        //$distinct_terms =  $this->trainingTerm->select('term', DB::raw('count(*) as total'))->orderBy("id")->groupBy("term")->pluck('term')->toArray();
        $categories = Category::orderBy("id")->get();
        $path = "csv/".$request->project_id."_automatic_assoc.csv";   
        /*$termsPerCategory = [];
        foreach ($categories as $category) {
            $termsPerCategory[$category->category] = TrainingTerm::select('term')->where("category_id",$category->id)->pluck('term')->toArray();
        }
        */
        $matriks = $corr->buildWordProprotionMatrice($this->trainingTerm,$categories);
        //dd($matriks);
        $fileHandler->writeCSV($path,$matriks,null);
        return  response()->json([
            'status' => 'success',
            'message' => 'matrice successfully created',
        ]);

    }
    
    public function show($id)
    {
        $trainingTerm = $this->trainingTerm->find($id);  
        if(empty($trainingTerm)){
            return $this->respondNotFound();
        }
        return response()->json($this->termTransformer->transform($trainingTerm));
    }

    public function store(TrainingTermRequest $request){
        $this->trainingTerm->term = $request->term;
        $this->trainingTerm->category_id = $request->category_id;
        $this->trainingTerm->project_id = $request->project_id;
        try{            
            $this->trainingTerm->save();
        }catch(\Exception $e){
            return response()->json(['error' => true, 'message' => $e->getMessage()]);
        }

        return response()->json(['error' => false, 'message' => 'Term success created']);
    }

     
    public function update(TrainingTermRequest $request, $id)
    {
        $trainingTerm = $this->trainingTerm->find($id);  
        if(empty($trainingTerm)){
            return $this->respondNotFound();
        }
        $trainingTerm->term = $request->term;
        $trainingTerm->category_id = $request->category_id;
        try{            
            $trainingTerm->save();
        }catch(\Exception $e){
            if($e->getCode() == "23000"){
                return response()->json(['error' => true, 'message' => 'Failed, Other Data Refrence this data']);
            }
            
            return response()->json(['error' => true, 'message' => 'There is problem on server']);
        }

        return response()->json(['error' => false, 'message' => 'term successfully updated']);
    }

    public function destroy($id)
    {
        $trainingTerm = $this->trainingTerm->find($id);
        if(empty($trainingTerm)){
            return $this->respondNotFound();
        }
        try{            
            $trainingTerm->delete();
        }catch(\Exception $e){
             if($e->getCode() == "23000"){
                return response()->json(['error' => true, 'message' => 'Failed, Other Data Refrence this data']);
            }
            return response()->json(['error' => true, 'message' => $e->getMessage()]);
        }

        return response()->json(['error' => false, 'message' => 'term success deleted']);
  
    }

    
}
