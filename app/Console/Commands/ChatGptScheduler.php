<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionLike;
use App\Model\Thread;
use App\Model\ThreadOpinion;
use App\Model\User;
use App\Model\UserDevice;
use Carbon\Carbon;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class ChatGptScheduler extends Command
{
    protected $signature = 'chatgpt:schedule';

    protected $description = 'Schedule a query to the ChatGPT API';

    public function handle()
    {
        $headlines = DB::table('news_headlines')
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();


            foreach ($headlines as $headline) {
                $client = new Client();
                $response = $client->post('https://api.openai.com/v1/chat/completions', [
                    'headers' => [
                        'Authorization' => 'Bearer ' .'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',
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'model' => 'gpt-3.5-turbo',
                        'messages' => [
                            [
                                'role' => 'user',
                                'content' => 'write a controversial opinion in less than 100 words about this news '.$headline->title.' also must add one-two hash tags in between',
                            ],
                        ],
                    ],
                ]);

                $result = json_decode($response->getBody()->getContents(), true);
                $opinion = $result['choices'][0]['message']['content'];

                echo "Opinion is ".$opinion."\n"; 
                echo "Headline id is ".$headline->title."\n";
                $this->saveOpiniontoDb($opinion,"none",$headline->id,"".$headline->title);
                echo "Opinion saved to db\n";

                //Find a way to avoid error 429 too many requests
                sleep(10);

            }
        
     

        $this->info('Query completed successfully!');
    }

    public function saveOpiniontoDb($gptBody,$gptType,$gptNewsId,$title){
        $cover=NULL;
        $opinion_uuid=uniqid();
        $type=$gptType;
 
        $community_id = 0;
        $news_id = $gptNewsId;
        $opinion_title = $title;
        $thumbnail=NULL;

        $body_temp = $gptBody;
                $blacklistArray = ['iframe'];
                $flag = false;
                foreach ($blacklistArray as $k => $v) {
                  if (str::contains($body_temp, $v)) {
                    $flag = true;
                    break;
                  }
                }

                if ($flag == true) {
                  $body = strip_tags("$body_temp");
                }
                else{
                  $body=$gptBody;
                }
                $plain_body=$body;
                $cpanel_body=$body;

                $hash_pattern="/#(\w+)/";
                preg_match_all($hash_pattern, $body, $hashtags);
                $opinion_threads=[];

            

                // finding # tags in opinion body
                if(count($hashtags[1])>0){
                    $hash_tags_to_store=implode(',',array_map(function ($str) { return "#$str"; },$hashtags[1]));
                    foreach ($hashtags[1] as $hashtag) {
                        $thread_found=Thread::whereRaw('LOWER(`name`) = ?',[trim(strtolower($hashtag))])->first();
                            if($thread_found){
                                $thread_id=$thread_found->id;
                            }else{
                                $thread_create=Thread::create(['name'=>$hashtag,'slug'=>str::slug(trim($hashtag),'-')]);
                                $thread_id=$thread_create->id;
                            }
                        array_push($opinion_threads,$thread_id);
                        $body=str_replace('#'.$hashtag,'<a href="https://weopined.com/thread/'.$hashtag.'" data-id="'.$thread_id.'" class="thread_link">#'.$hashtag.'</a>',$body);
                        $cpanel_body=str_replace('#'.$hashtag,'<a href="https://weopined.com/cpanel/thread/view/'.$thread_id.'" class="thread_link">#'.$hashtag.'</a>',$cpanel_body);
                    }
                }else{
                   
                }

                // finding links in opinion body and get info of links
                $pattern  = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#';
                preg_match_all($pattern,$gptBody, $matches);
                $all_urls = $matches[0];
                if(count($all_urls)>0){
                    $infolinks=array();
                    foreach($all_urls as $url){
                        $body=str_replace($url,'',$body);
                        $info=$this->fetch_data_from_url($url);
                        if($info){
                            array_push($infolinks,$info);
                        }
                    }
                    $links_enc=json_encode($infolinks);
                    $links_dummy=json_decode($links_enc);
                      foreach ($links_dummy as $index=>$link_dummy) {
                 
                         if($link_dummy->status=="error" || $link_dummy->image=="null"){
                              $links = NULL;
                              //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                          }
                          else{
                            $links=json_encode($infolinks);
                          }

                        }
                }else{
                    $links=NULL;
                }


                $opinion=new ShortOpinion();
                $data=[];
                $data['title']="".$opinion_title;
                $data['body']=$body;
                $data['plain_body']=$plain_body;
                $data['cpanel_body']=$cpanel_body;
                $data['hash_tags']=$hash_tags_to_store;
                $data['cover']=$cover; 
                $data['type']=$type;
                $data['links']=$links;
                $data['thumbnail']=$thumbnail;
                $data['community_id'] = $community_id;
                $data['news_id'] = $news_id;
                $opinion=$this->save_opinion($opinion,$data,$opinion_uuid);

                if(count($opinion_threads)>0){
                    $opinion->threads()->sync(array_unique($opinion_threads));
                }
    }
    protected function save_opinion(ShortOpinion $opinion,array $data,$unique_id){
        $opinion->uuid=$unique_id;
        $opinion->title=isset($data['title'])?$data['title']:'';
        $opinion->body=isset($data['body'])?$data['body']:'';
        $opinion->plain_body=isset($data['plain_body'])?$data['plain_body']:NULL;
        $opinion->cpanel_body=isset($data['cpanel_body'])?$data['cpanel_body']:NULL;
        $opinion->hash_tags=isset($data['hash_tags'])?$data['hash_tags']:NULL;
        $opinion->cover=isset($data['cover'])?$data['cover']:NULL;
        $opinion->cover_type=isset($data['type'])?$data['type']:'none';
        $opinion->links=isset($data['links'])?$data['links']:NULL;
        $opinion->thumbnail=isset($data['thumbnail'])?$data['thumbnail']:NULL;
        $opinion->user_id=rand(4406,4700);;
        $opinion->platform=isset($data['platform'])?$data['platform']:'android';
        $opinion->community_id=isset($data['community_id'])?$data['community_id']:0;
        $opinion->news_id=isset($data['news_id'])?$data['news_id']:0;
        $opinion->save();
        return $opinion;
    }
}
