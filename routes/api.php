<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'AuthController@login');
Route::group(['middleware' => ['jwt.auth']], function() {
    Route::get('/complaints', 'ComplaintController@index');
    Route::post('/complaints/upload-csv', 'ComplaintController@uploadCSV');
    Route::post('/complaints', 'ComplaintController@store');
    Route::get('/complaints/category/{category_slug}', 'ComplaintController@indexByPredicted');
    Route::post('/complaints/categorize/cc', 'ComplaintController@categorize_cc');
    Route::post('/complaints/categorize/bayes', 'ComplaintController@categorize_bayes');
    Route::get('/complaints/topic-models/{category_slug}', 'ComplaintController@topic_modelling');

    Route::get('/terms', 'TermController@index');
    Route::get('/terms/search/{q}', 'TermController@search');    
    Route::post('/terms/generate-assoc', 'TermController@generateTermAssoc');
    Route::get('/terms/{id}', 'TermController@show');
    Route::put('/terms/{id}', 'TermController@update');
    Route::delete('/terms/{id}', 'TermController@destroy');
    Route::post('/terms', 'TermController@store');

    Route::get('/training-terms', 'TrainingTermController@index');
    Route::get('/training-terms/search/{q}', 'TrainingTermController@search');    
    Route::post('/training-terms/generate-assoc', 'TrainingTermController@generateTermAssoc');
    Route::get('/training-terms/{id}', 'TrainingTermController@show');
    Route::put('/training-terms/{id}', 'TrainingTermController@update');
    Route::delete('/training-terms/{id}', 'TrainingTermController@destroy');
    Route::post('/training-terms', 'TrainingTermController@store');

    Route::get('/categories', 'CategoryController@index');
    Route::get('/categories/{id}', 'CategoryController@show');
    Route::put('/categories/{id}', 'CategoryController@update');
    Route::delete('/categories/{id}', 'CategoryController@destroy');
    Route::post('/categories', 'CategoryController@store');


    Route::post('/trainings/clean-data','TrainingController@cleanData');
    Route::post('/trainings/bayes','TrainingController@trainBayes');
    Route::post('/trainings/binary-matrice','TrainingController@binaryMatrice');
    Route::post('/trainings/extract-keywords','TrainingController@extractKeyword');
    Route::get('/trainings', 'TrainingController@index');
    Route::get('/trainings/score/{method}', 'TrainingController@showScore');


    Route::post('/tweets/crawl/{query}', 'TweetController@crawling');
    Route::get('/tweets/clean', 'TweetController@clean');
    
    Route::get('/dashboard', 'DashboardController@dashboard');
    Route::get('/dashboard/chart', 'DashboardController@getChartData');
    Route::get('/dashboard/periode', 'DashboardController@dashboardByMonth');    
    Route::get('/dashboard/{category_slug}/clustered-words', 'DashboardController@getClusteredTopWords');
    Route::get('/dashboard/{category_slug}/{mode}', 'DashboardController@get_dashboard_category');

});

Route::group([ 'middleware' => ['jwt.auth'],'prefix'=> 'mobile','namespace'=>'Mobile'], function() {
    Route::get('/dashboard', 'DashboardController@dashboard');
    Route::get('/tweets', 'TweetController@index');
});