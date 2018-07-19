<?php

namespace App\Helper;

use App\Helper\Tweet;
use App\Helper\TweetCriteria;
use Symfony\Component\DomCrawler\Crawler;


/**
 * @author yawmoght <yawmoght@gmail.com>
 */
class TweetManager
{
    protected $client;

    /**
     * TweetManager constructor.
     * @param $client
     */
    function __construct()
    {
    }

    /**
     * @param TweetCriteria $criteria
     * @return Tweet[]
     */
    public function getTweets(TweetCriteria $criteria,$lastTimeStamps)
    {
        $results = array();
        try {
            $refreshCursor = null;

            if ($criteria->getMaxTweets() == 0){
                return $results;
            }
            do {
                $response = $this->getUrlResponse($criteria->getUsername(),
                    $criteria->getSince(),
                    $criteria->getUntil(),
                    $criteria->getQuerySearch(),
                    $refreshCursor);
                $refreshCursor = $response['min_position'];
                $htmlCrawler = new Crawler($response['items_html']);
                $tweetsCrawler = $htmlCrawler->filter('div.js-stream-tweet');  
                if ($tweetsCrawler->count() == 0) {
                    break;
                }

                $tweetsCrawler->each(function ($tweet) use (&$results,$lastTimeStamps) {
                    /** @var $tweet \Symfony\Component\DomCrawler\Crawler */
                    $username = $tweet->filter('span.username.u-dir.u-textTruncate b')->first()->text();
                    $text = str_replace('[^\\u0000-\\uFFFF]', '', $tweet->filter('p.js-tweet-text')->first()->text());
                    $retweets = intval(str_replace(',', '', $tweet->filter('span.ProfileTweet-action--retweet span.ProfileTweet-actionCount')->first()->attr('data-tweet-stat-count')));
                    $favorites = intval(str_replace(',', '', $tweet->filter('span.ProfileTweet-action--favorite span.ProfileTweet-actionCount')->first()->attr('data-tweet-stat-count')));
                    $timestamp = floatval($tweet->filter('small.time span.js-short-timestamp')->first()->attr('data-time-ms'))/1000;
                    $date = new \DateTime('@'.$timestamp);
                    $id = $tweet->first()->attr('data-tweet-id');
                    $permalink = $tweet->first()->attr('data-permalink-path');   
                    preg_match("(@\\w*)", $text, $mentions);
                    preg_match("(#\\w*)", $text, $hashtags);
                    

                    $geo = '';
                    $geoElement = $tweet->filter('span.Tweet-geo')->first();
                    if ($geoElement->count() > 0) {
                        $geo = $geoElement->attr('title');
                    }

                    $resultTweet = new Tweet();
                    $resultTweet->setId($id);
                    $resultTweet->setPermalink("https://twitter.com" . $permalink);
                    $resultTweet->setUsername($username);
                    $resultTweet->setTimeStamp($timestamp);
                    $resultTweet->setText($text);
                    $resultTweet->setDate($date);
                    $resultTweet->setRetweets($retweets);
                    $resultTweet->setFavorites($favorites);
                    $resultTweet->setMentions($mentions);
                    $resultTweet->setHashtags($hashtags);
                    $resultTweet->setGeo($geo);

                    if($username != "LAPOR1708"){
                        if($lastTimeStamps < $timestamp){                            
                             $results[] = $resultTweet;
                        }
                    }
                });

            } while (count($results) < $criteria->getMaxTweets());

        } catch (\Exception $e) {
            $this->handleException($e);
            return $results;
        }

                    
        return $results;
    }


    /**
     * @param $username
     * @param $since
     * @param $until
     * @param $querySearch
     * @param $scrollCursor
     * @return mixed
     */
    public function getUrlResponse($username, $since, $until, $querySearch, $scrollCursor)
    {
        $appendQuery = "";

        if ($username != null) {
            $appendQuery .= 'from:' . $username;
        }

        if ($since != null) {
            $appendQuery .= ' since:' . $since;
        }

        if ($until != null) {
            $appendQuery .= ' until:' . $until;
        }

        if ($querySearch != null) {
            $appendQuery .= ' ' . $querySearch;
        }
        
        $url = "https://twitter.com/i/search/timeline?f=tweets&q=+LAPOR1708&src=typd&max_position=".$scrollCursor;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        $result=curl_exec($ch);
        curl_close($ch);
        return (json_decode($result, true));
    }

    protected function handleException(\Exception $e)
    {

        dd($e);
        //Insert your method here.
    }


}