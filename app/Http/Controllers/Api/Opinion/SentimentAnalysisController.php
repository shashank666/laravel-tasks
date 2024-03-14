<?php

namespace App\Http\Controllers\Api\Opinion;

use Aws\Comprehend\ComprehendClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SentimentAnalysisController extends Controller
{
    public function analyzeSentiment(Request $request)
    {
        $body = $request->input('body');

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

        // Call the AWS Comprehend API to detect sentiment
        $result = $client->detectSentiment([
            'LanguageCode' => 'en',
            'Text' => $body,
        ]);

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

        return response()->json([
            'sentiment' => $sentiment,
            'opinionScore' => $opinionScore,
        ]);
    }
}
