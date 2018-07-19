<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helper\LDATopicModels;
use App\Project;
use App\Category;
use App\Complaint;

class ProjectController extends Controller
{
    public function index(){
    	$projects = Project::all();
    	return view("projects/index",["projects" => $projects]);
    }

    public function show($slug){
    	$project = Project::where("slug",$slug)->first();
        
    	return view("projects/view",["project" => $project]);
    }

    public function analyzeByCategory($slug,$category_slug){
    	$category = Category::where("slug",$category_slug)->first();
    	$project = Project::where("slug",$slug)->first();
    	$complaints = Complaint::where(["project_id" => $project->id,"predicted_category" => $category->category])->get();
        
        $lda = new LDATopicModels();
        $results = $lda->modelling($complaints,4);
        $results = array_slice( $results, 0,4);
        return view("projects/analyze",["results" => $results,
                                        "project" => $project,
                                        "category_slug" => $category_slug
                                    ]);
    }

}
