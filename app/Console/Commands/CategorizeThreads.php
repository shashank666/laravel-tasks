<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Model\Thread;
use App\Model\Category;
use App\Model\CategoryThread;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class CategorizeThreads extends Command
{
    protected $signature = 'threads:categorize';
    protected $description = 'Categorize threads';

    public function handle()
{
   
    
    $threads = Thread::whereBetween('id', [5301, 5370])->get();
    
    $threadNames = $threads->pluck('name')->toArray();
    $openaiApiKey = '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'; 

    $categoryNames = [];
    $string = implode(', ', $threadNames);
    
        $client = new Client();

        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $openaiApiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant that provides One Word General category names based on thread names. Give Back List of map of key thread name and value category name. For example - if thread name is tech2021 then category name is technology.',
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Threads: ' . $string,
                    ],
                ],
            ],
        ]);

        $responseData = json_decode($response->getBody(), true);
        $categoryNames  = explode(", ", $responseData['choices'][0]['message']['content']);
        
        foreach($categoryNames as $key => $value){
         
            $categoryData = json_decode($value, true);

            $threadsNames = [];
            $categories = [];

            // Extract thread names and category names
            foreach ($categoryData as $threadName => $categoryName) {
                $threadsNames[] = $threadName;
                $categories[] = $categoryName;
            }

            echo "threads: ".print_r($threadsNames, true)."\n";
            echo "categories: ".print_r($categories, true)."\n";




    $lowercaseCategories = array_map('strtolower', $categories);

    $existingCategories = Category::whereIn(DB::raw('LOWER(name)'), $lowercaseCategories)
    ->where('is_active', 1)
    ->get();

    $existingCategoryNames = $existingCategories->pluck('name')->toArray();



echo "existingCategoryNames: ".print_r($existingCategoryNames, true)."\n";

for ($i =0; $i < count($categories); $i++) {
   $categoryName = $categories[$i];
   $threadName = $threadsNames[$i];

     echo "categoryName: ".print_r($categoryName, true)."\n";
        echo "threadName: ".print_r($threadName, true)."\n";
     if (!in_array($categoryName, $existingCategoryNames)) {
        $thread = $threads->firstWhere('name', $threadName);

        if($thread!=null && $category!=null){
            $existingEntry = CategoryThread::where('thread_id', $thread->id)
            ->where('category_id', $category->id)
            ->exists();
        
            if (!$existingEntry) {
                // Create a new category_thread entry
                CategoryThread::create([
                    'thread_id' => $thread->id,
                    'category_id' => 449,
                ]);
            }
        }
    }else{
        $category = $existingCategories->firstWhere('name', $categoryName);
        $thread = $threads->firstWhere('name', $threadName);

        if($thread!=null && $category!=null){
            $existingEntry = CategoryThread::where('thread_id', $thread->id)
            ->where('category_id', $category->id)
            ->exists();
        
            if (!$existingEntry) {
                // Create a new category_thread entry
                CategoryThread::create([
                    'thread_id' => $thread->id,
                    'category_id' => $category->id,
                ]);
            }
        }
    }
   

   
    


}
            
        }



    $this->info('Threads categorized successfully.');
}

   
    
    
}
