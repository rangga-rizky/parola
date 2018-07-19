<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function(){
	return view('index');
});
Route::get('/validation/loo', 'ValidationController@loo');
Route::get('/validation/clean', 'ValidationController@clean_data');
Route::get('/validation/dist', 'ValidationController@dist');
Route::get('/validation/binary-matrix', 'ValidationController@binaryMatrix');
Route::get('/validation/loo/score', 'ValidationController@score');
Route::get('/validation/bayes', 'ValidationController@bayes');
Route::get('/validation/manual', 'ValidationController@manual');

Route::get('/report/{project_slug}/print-pdf', 'ReportController@printPdf');



/*Route::get('/tweets/clean', 'TweetController@clean');
Route::get('/tweets/{project_slug}', 'TweetController@index');

Route::get('/projects', 'ProjectController@index');
Route::get('/projects/{slug}/analyze/{category_slug}', 'ProjectController@analyzeByCategory');
Route::get('/projects/{slug}', 'ProjectController@show');

Route::get('/trainings/project/{project_slug}', 'TrainingController@showByProject');

Route::get('/terms/project/{project_slug}', 'TermController@showByProject');

Route::get('/training-terms/project/{project_slug}', 'TrainingTermController@showByProject');

Route::get('/categories/project/{project_slug}', 'CategoryController@showByProject');


Route::get('/complaints/categorize/bayes/{project_slug}', 'ComplaintController@categorize_with_bayes');
Route::get('/complaints/categorize/{project_slug}', 'ComplaintController@categorize_with_corr');



/*

Route::get('/keyword/extract', 'KeywordExtractionController@extract');
Route::get('/keyword/aggregation', 'KeywordExtractionController@aggregation');
Route::get('/keyword/extract/ngram', 'KeywordExtractionController@extract_ngram');


Route::get('/term/matriks', 'TermController@matriks');
Route::get('/term/matriks_training', 'TermController@matriks_training');
Route::get('/term/matriks_ngram', 'TermController@matriks_training_ngram');
Route::get('/term/matriks_occurrence', 'TermController@occurrence');
Route::get('/term/matriks_occurrence/tfidf', 'TermController@occurrence_tfidf');

Route::get('/categorize/cc', 'CategorizeController@cc');
Route::get('/categorize/cc/training', 'CategorizeController@cc_training');
Route::get('/categorize/cc/ngram', 'CategorizeController@cc_ngram');
Route::get('/categorize/bayes', 'CategorizeController@bayes');
Route::get('/categorize/bayes/gain', 'CategorizeController@bayes_gain');
Route::get('/categorize/matriks', 'CategorizeController@matriks');
Route::get('/categorize/frekuensi', 'CategorizeController@frekuensi');

Route::get('/topic_modelling/{k}', 'ModellingController@lda');

Route::get('/trainings', 'TrainingController@index');


Route::get('/training_terms', 'TrainingTermController@index');*/



