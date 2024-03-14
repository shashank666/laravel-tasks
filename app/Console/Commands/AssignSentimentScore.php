<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\Opinion\SentimentAnalysisController;
use App\Model\ShortOpinion;
use Illuminate\Console\Command;
use Aws\Comprehend\ComprehendClient;
use Carbon\Carbon;
use Illuminate\Http\Request;


class AssignSentimentScore extends Command
{
    protected $signature = 'sentiment:assign-score';

    protected $description = 'Assign sentiment scores to latest ten opinions';

    public function handle()
{
    //$opinions = ShortOpinion::orderByDesc('created_at')->take(10)->get(); //to latest opinions

    $last60Days = Carbon::now()->subDays(60);
    $opinions = ShortOpinion::withCount(['likes', 'comments'])
        ->where('created_at', '>=', $last60Days)
        ->orderByDesc('likes_count')
        ->orderByDesc('comments_count')
        ->take(50)
        ->get();

     // Retrieve AWS credentials from .env file
     $awsAccessKeyId = env('AWS_ACCESS_KEY_ID');
     $awsSecretAccessKey = env('AWS_SECRET_ACCESS_KEY');

     // Create an instance of the AWS Comprehend client
     $client = new ComprehendClient([
         'version' => 'latest',
         'region' => 'ap-south-1', // Replace with your desired AWS region
         'credentials' => [
             'key' => $awsAccessKeyId,
             'secret' => $awsSecretAccessKey,
         ],
     ]);

    
    foreach ($opinions as $opinion) {
        $body = $opinion->plain_body;

       
        
         // Call the AWS Comprehend API to detect sentiment
         $result = $client->detectSentiment([
             'LanguageCode' => 'en',
             'Text' => $body,
         ]);

         echo "Body: " . $body . "\n";
 
         // Calculate the OpinionScore based on the sentiment
         $sentiment = $result['Sentiment'];
         $sentimentScore = $result['SentimentScore'];
 
         if ($sentiment === 'NEGATIVE') {
             $opinionScore = $sentimentScore['Negative'] * 100 + 200;
         } elseif ($sentiment === 'POSITIVE') {
             $opinionScore = $sentimentScore['Positive'] * 100 + 100;
         } elseif ($sentiment === 'NEUTRAL') {
             $opinionScore = $sentimentScore['Neutral'] * 100;
         } elseif ($sentiment === 'MIXED') {
             $opinionScore = $sentimentScore['Mixed'] * 100 + 50;
         } else {
             $opinionScore = 0; // Default value if sentiment is unknown
         }
             if (strlen($body) < 100) {
            $opinionScore = $opinionScore * 0.25;
            } elseif (strlen($body) < 200) {
                $opinionScore = $opinionScore * 0.5;
            }else if (strlen($body) < 300) {
                $opinionScore = $opinionScore * 0.75;
            }else if (strlen($body) < 400) {
                $opinionScore = $opinionScore * 0.85;
            }else{
                $opinionScore = $opinionScore * 1;
            }
        $opinion->score = $opinionScore;
        $opinion->save();
    }

    $this->info('Sentiment scores assigned to latest ten opinions.');
}

}
