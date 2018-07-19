<?php

namespace App\Observers;
use App\Term;
use App\Category;
use App\Helper\FileHandler;
use App\Helper\CorrelationMeassure;

class TermObserver
{
    public function created(Term $term)
    {
        $this->generateTermAssoc($term->project->id,new CorrelationMeassure,new FileHandler);
    }

    public function updated(Term $term)
    {
        $this->generateTermAssoc($term->project->id,new CorrelationMeassure,new FileHandler);
    }

    public function deleted(Term $term)
    {
        $this->generateTermAssoc($term->project->id,new CorrelationMeassure,new FileHandler);
    }

    private function generateTermAssoc($project_id,$corr,$fileHandler){
        $distinct_terms = Term::select('term')->pluck('term')->toArray();   
        $path = "csv/".$project_id."_training_assoc.csv";
        $categories = Category::orderBy("id")->get();       
        $termsPerCategory = [];
        foreach ($categories as $category) {
            $termsPerCategory[$category->category] = Term::select('term')->where("category_id",$category->id)->pluck('term')->toArray();
        }
        $matriks = $corr->buildCorrMatrice($distinct_terms,$termsPerCategory,$categories);
        $fileHandler->writeCSV($path,$matriks,null);
    }
}