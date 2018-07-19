<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\Tweet;
use App\Category;
use App\Helper\FileHandler;
use Carbon\Carbon;
use DB;
use PDF;
use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;

class ReportController extends Controller
{
    public function __construct(Tweet $tweet,Project $project,Category $category,FileHandler $fileHandler){
        $this->tweet = $tweet;
        $this->project = $project;
        $this->category = $category;
        $this->fileHandler = $fileHandler;
    }

    public function printPdf($project_slug){
        $project = $this->project->where('slug',$project_slug)->first();
        $tweets = $this->tweet->orderBy('date','DESC')->get();
        $most_category = DB::select('call get_most_frequent_category()');
        $categories = $this->category->all();
        $data["n_data"] = $tweets->count();
        $data["tweets"] = $tweets->take(100);
        $data["last_data"] = Carbon::parse($tweets->first()->date)->formatLocalized('%H:%S %A, %d %B %Y');
        $data["title"] = $project->name;
        $data["most_category"] = $most_category[0]->predicted;
        $data["n_categories"] = $categories->count();
        $data["top_words"] = $this->getTopWords(null,null);
        $dist = $this->getDistCategory(null,null);  
        $this->drawPie(550, 450, $dist["values"],$dist["labels"]);
        $pdf = PDF::loadView('pdf.report',$data);

        //return response()->view('pdf.report',$data);
        return $pdf->inline();
    }

    private function drawPie($w,$h,$values,$labels){        
        $graph = new Graph\PieGraph($w,$h);
        $graph->SetBox(true);
     
        $p1   = new Plot\PiePlot($values);
        $p1->ShowBorder();
        $p1->SetColor('black');
        $p1->SetSliceColors(array('#1E90FF', '#2E8B57', '#ADFF2F', '#DC143C', '#BA55D3','#1EFFFF', '#2EEE57', '#AD222F', '#DCC43C', '#BA5AA3'));
        $p1->SetLegends($labels);
        $graph->Add($p1);
        @unlink("chart/pie.jpg");
        $graph->Stroke("chart/pie.jpg");
    }

    private function getTopWords($month,$year){
        if(!is_null($month) && !is_null($year)){
          //  return $this->getTopwordByMonth($month,$year);
        }

        $distinct_terms = $this->fileHandler->readKeyPairCSV("csv/tweet_distinct_terms.csv",false);
        arsort($distinct_terms);
        $topWords = array_slice( $distinct_terms, 0,10); 
        return $topWords;
    }

    private function getComplaintFreq($category){
        if(is_null($category)){
            $n_of_data = DB::select("CALL get_complaint_freq()");
        }else{
            $q = 
            $n_of_data = DB::select("CALL get_complaint_freq_by_category('$category')");
        }     
        
        return $n_of_data;
    }

    private function getDistCategory($month,$year){
        if(!is_null( $month) && !is_null($year)){
            $number_of_predicted = DB::select("CALL get_number_of_predicted_by_month('$month','$year')");
        }else{            
            $number_of_predicted = DB::select("CALL get_number_of_predicted()");
        }
        foreach ($number_of_predicted as  $data) {
            $labels[] = $data->predicted;
            $values[] = $data->jumlah;
        }
        return array(
            'labels'=> $labels,
            'values' => $values
        );
    }

}
