<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TermRequest;
use App\Term;
use App\Category;
use App\Helper\CorrelationMeassure;
use App\Helper\FileHandler;
use App\Transformers\TermTransformer;

class TermController extends Controller
{

	public function __construct(Term $term,TermTransformer $termTransformer){
		$this->term = $term;
		$this->termTransformer = $termTransformer;
	}
    public function index(Request $request){

        $limit = $request->input('limit') ?: 15;
        $category_id  = $request->input('category_id');
        if(is_null($category_id)){
            $terms = $this->term->paginate($limit);
        }else{
            $terms = $this->term->where(["category_id" => $category_id])->paginate($limit);
        }

		return  $this->respondWithPagination($terms, [
            'data' =>  $this->termTransformer->transformCollection($terms)
        ]);
    }

    public function search(Request $request,$q){
        $limit = $request->input('limit') ?: 15;
        $terms = $this->term->where('term', 'like', '%' . $q . '%')->paginate($limit);
        return  $this->respondWithPagination($terms, [
            'data' =>  $this->termTransformer->transformCollection($terms)
        ]);
    }   

    public function generateTermAssoc(Request $request){
        $corr = new CorrelationMeassure();
        $fileHandler = new FileHandler();
        $distinct_terms = Term::select('term')->pluck('term')->toArray();   
        $path = "csv/".$request->project_id."_training_assoc.csv";
        $categories = Category::orderBy("id")->get();       
        $termsPerCategory = [];
        foreach ($categories as $category) {
            $termsPerCategory[$category->category] = Term::select('term')->where("category_id",$category->id)->pluck('term')->toArray();
        }
        $matriks = $corr->buildCorrMatrice($distinct_terms,$termsPerCategory,$categories);
        $fileHandler->writeCSV($path,$matriks,null);
        return  response()->json([
            'status' => 'success',
        ]);

    }

    public function show($id)
    {
        $term = $this->term->find($id);  
        if(empty($term)){
            return $this->respondNotFound();
        }
        return response()->json($this->termTransformer->transform($term));
    }

    public function store(TermRequest $request){
        $this->term->term = $request->term;
        $this->term->category_id = $request->category_id;
        $this->term->project_id = $request->project_id;
        try{            
            $this->term->save();
        }catch(\Exception $e){
            return response()->json(['error' => true, 'message' => $e->getMessage()]);
        }

        return response()->json(['error' => false, 'message' => 'Term success created']);
    }

     
    public function update(TermRequest $request, $id)
    {
        $term = $this->term->find($id);  
        if(empty($term)){
            return $this->respondNotFound();
        }
        $term->term = $request->term;
        $term->category_id = $request->category_id;
        try{            
            $term->save();
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
        $term = $this->term->find($id);
        if(empty($term)){
            return $this->respondNotFound();
        }
        try{            
            $term->delete();
        }catch(\Exception $e){
             if($e->getCode() == "23000"){
                return response()->json(['error' => true, 'message' => 'Failed, Other Data Refrence this data']);
            }
            return response()->json(['error' => true, 'message' => 'There is problem on server']);
        }

        return response()->json(['error' => false, 'message' => 'term success deleted']);
  
    }
}
