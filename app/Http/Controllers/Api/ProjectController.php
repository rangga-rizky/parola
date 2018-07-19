<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\TextMiner;
use App\Project;
use App\TrainingTerm;
use App\Category;
use App\Complaint;

class ProjectController extends Controller
{
     public function topWordsperCategory($slug,$category_slug){
    	$texMiner = new TextMiner();
    	$category = Category::where("slug",$category_slug)->first();
    	$terms = TrainingTerm::orderBy('id')->pluck('term')->toArray(); 
    	$project = Project::where("slug",$slug)->first();
    	$complaints = Complaint::where(["project_id" => $project->id,"predicted_category" => $category->category])->get();
    	$clean_data = $texMiner->textPreprocessing($complaints);
    	
    	foreach ($terms as $term) {
    		$wordCount[$term] = 0;
    	}

    	foreach ($clean_data["tokenize_data"] as $clean_text) {
    		foreach ($terms as $term) {
    			if(in_array($term, $clean_text) ){
    				$wordCount[$term]++;
    			}
    		}
    	}

    	arsort($wordCount);
		$topWords = array_slice( $wordCount, 0,30);
		return response()->json([
		    "word_count" => $topWords,
		]);
    }
}
