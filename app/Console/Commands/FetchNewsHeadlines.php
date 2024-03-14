<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use GuzzleHttp\Client;

class FetchNewsHeadlines extends Command
{
    protected $signature = 'fetch:news-headlines';
    protected $description = 'Fetch latest news headlines and save to database';
   

    public function handle()
    {
        // $newsapi = new NewsApi('0360e3e2d1dc4e8da356af129a30c807');
        $client = new Client();
        $response = $client->get('https://inshorts-news.vercel.app/all');

        //$articles = $newsapi->getTopHeadlines("us")->articles;
        $data = json_decode($response->getBody()->getContents());

        

        foreach ($data->data as $article) {
            
        $existingArticle = DB::table('news_headlines')
            ->where('url', $article->{'inshorts-link'})
            ->first();
        if ($existingArticle) {
            continue; // Skip if article already exists
        }

        // Insert new article
        DB::table('news_headlines')->insert([
            'title' => $article->title,
            'description' => $article->decription,
            'url' => $article->{'inshorts-link'},
            'url_to_image' => $article->images,
            'published_at' => $article->time,

            'source_name' => $article->author,
            'created_at' => now(),
        ]);
            }
            $this->info('News headlines fetched and saved to database successfully!');
        }

        
    
}
