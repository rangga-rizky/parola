<?php

namespace App\Observers;
use App\TrainingTerm;
use App\Category;
use App\Helper\FileHandler;
use App\Helper\CorrelationMeassure;
use DB;

class TrainingTermObserver
{
    public function created(TrainingTerm $term)
    {
        $this->generateTermAssoc($term->project->id,new CorrelationMeassure,new FileHandler);
    }

    public function updated(TrainingTerm $term)
    {
        $this->generateTermAssoc($term->project->id,new CorrelationMeassure,new FileHandler);
    }

    public function deleted(TrainingTerm $term)
    {
        $this->generateTermAssoc($term->project->id,new CorrelationMeassure,new FileHandler);
    }

    private function generateTermAssoc($project_id,$corr,$fileHandler){
        $distinct_terms =  TrainingTerm::select('term', DB::raw('count(*) as total'))->groupBy("term")->pluck('term')->toArray();
        $categories = Category::orderBy("id")->get();
        $path = "csv/".$project_id."_automatic_assoc.csv";   
        $termsPerCategory = [];
        foreach ($categories as $category) {
            $termsPerCategory[$category->category] = TrainingTerm::select('term')->where("category_id",$category->id)->pluck('term')->toArray();
        }
        $matriks = $corr->buildCorrMatrice($distinct_terms,$termsPerCategory,$categories);
        $fileHandler->writeCSV($path,$matriks,null);

    }
}