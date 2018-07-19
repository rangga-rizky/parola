<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use App\Helper\SlugGenerator;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    
	public function __construct(Category $category){
		$this->category = $category;
	}

    public function index(){ 
        $user = $this->getUser();
    	$categories = $this->category->where('project_id',$user->project->id)->get();
    	return response()->json($categories);
	}
	
	public function show($id){
        $category = $this->category->find($id);  
        if(empty($category)){
            return $this->respondNotFound();
        }
        return response()->json($category);
    }

    public function store(CategoryRequest $request){
		$slugGenerator = new SlugGenerator;
        $this->category->category = $request->category;
        $this->category->slug = $slugGenerator->generate($request->category,$this->category);
        $this->category->project_id = $request->project_id;
        try{            
            $this->category->save();
        }catch(\Exception $e){
            return response()->json(['error' => true, 'message' => $e->getMessage()]);
        }

        return response()->json(['error' => false, 'message' => 'Category success created']);
    }

     
    public function update(CategoryRequest $request, $id)
    {
		$slugGenerator = new SlugGenerator;
        $category = $this->category->find($id);  
        if(empty($category)){
            return $this->respondNotFound();
        }
        $category->category = $request->category;
        $category->slug = $slugGenerator->generate($request->category,$this->category);
        $category->project_id = $request->project_id;
        try{            
            $category->save();
        }catch(\Exception $e){
            if($e->getCode() == "23000"){
                return response()->json(['error' => true, 'message' => 'Failed, Other Data Refrence this data']);
            }
            
            return response()->json(['error' => true, 'message' => 'There is problem on server']);
        }

        return response()->json(['error' => false, 'message' => 'Category successfully updated']);
    }

    public function destroy($id)
    {
        $category = $this->category->find($id);
        if(empty($category)){
            return $this->respondNotFound();
        }
        try{            
            $category->delete();
        }catch(\Exception $e){
             if($e->getCode() == "23000"){
                return response()->json(['error' => true, 'message' => 'Failed, Other Data Refrence this data']);
            }
            return response()->json(['error' => true, 'message' => 'There is problem on server']);
        }

        return response()->json(['error' => false, 'message' => 'Category success deleted']);
  
    }

}
