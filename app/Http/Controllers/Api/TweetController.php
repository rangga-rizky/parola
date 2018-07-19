<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ForceUTF8\Encoding;
use App\Helper\TweetManager;
use App\Helper\TweetCriteria;
use App\Helper\TextMiner;
use App\Helper\KeywordExtractor;
use App\Helper\FileHandler;
use App\Project;
use App\Tweet;

class TweetController extends Controller
{

	public function __construct(TweetManager $tweetCrawler,TweetCriteria $tweetCriteria)
    {
        $this->tweetCrawler = $tweetCrawler;
        $this->tweetCriteria = $tweetCriteria;
        $this->textMiner = new TextMiner();
        $this->fileHandler = new FileHandler();
    }


   
    public function crawling($query){    
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 5000);     
        $lastestTimeStampTweet = Tweet::max("timestamp");
        if(empty($lastestTimeStampTweet)){
            $lastestTimeStampTweet = 0;
        }
        $distinct_terms = $this->fileHandler->readKeyPairCSV('csv/tweet_distinct_terms.csv');
        $this->tweetCriteria->setQuerySearch($query);
        $this->tweetCriteria->setMaxTweets(100);
        $tweets = $this->tweetCrawler->getTweets($this->tweetCriteria,$lastestTimeStampTweet);
        foreach ($tweets as $tweet_data) {
            $tweet = new Tweet;
            $utf8_string = Encoding::fixUTF8($tweet_data->getText());
            $words = $this->textMiner->textPreprocessing($this->textMiner->removeTweetAttr($utf8_string));
            $tweet->create([
                'username' => $tweet_data->getUsername(),
                'tweet' => $utf8_string,
                'clean_tweet' => implode(",", $words),
                'timestamp' => $tweet_data->getTimeStamp(),
                'date' => $tweet_data->getDate()->format('Y-m-d H:i:s'),
            ]);
            foreach ($words as $word) {
                if (array_key_exists($word,$distinct_terms)){
                  $distinct_terms[$word] = intval($distinct_terms[$word]) + 1;
                }else{
                  $distinct_terms[$word] = 1;
                }    
            }            
        }

        foreach ($distinct_terms as $word => $value) {
            $terms[] = array($word,$value);
        }       
        $this->fileHandler->writeCSV('csv/tweet_distinct_terms.csv',$terms,null);

        return response()->json([
            "error" => False,
            "messages" => sizeof($tweets)." tweets berhasil masuk"
        ]);

    }

    public function clean(){            
        $keywordExtractor = new KeywordExtractor(); 
        $tweets = Tweet::all();
        $distinct_terms = [];
        $tokenized_text = [];
        foreach ($tweets as $tweet) {
            $tokenized_text[] = $this->textMiner->textPreprocessing($tweet->clean_tweet);       
        }
        $distinct_terms = $this->textMiner->getDistinctTerm();
        $topWords = $keywordExtractor->aggregate($tokenized_text,$distinct_terms,sizeof($distinct_terms));
        $words = [];
        foreach ($topWords as $word => $value) {
            $words[] = array($word,$value);
        }       
        $this->fileHandler->writeCSV('csv/tweet_distinct_terms.csv',$words,null);
         return response()->json([
            "error" => False,
            "messages" => "Data berhasil dibersihkan"
        ]);
    }



   
}
