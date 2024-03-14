<?php

namespace App\Jobs\Digest;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Redis;

use Mail;
use App\Mail\Digest\SendWeeklyDigestMail;
use App\Http\Helpers\MailJetHelper;
require 'vendor/autoload.php';
use \Mailjet\Resources;


class WeeklyDigestMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 3;
    public $timeout = 14400; // 4 hours

    public $opinions,$latest_posts,$trending_threads,$contributors,$top_polls_trending,$users;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($opinions,$latest_posts,$trending_threads,$contributors,$top_polls_trending,$users)
    {
        $this->opinions=$opinions;
        $this->latest_posts=$latest_posts;
        $this->trending_threads=$trending_threads;
        $this->contributors=$contributors;
        $this->top_polls_trending=$top_polls_trending;
        $this->users=$users;
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $opinions=$this->opinions;
        $latest_posts=$this->latest_posts;
        $trending_threads=$this->trending_threads;
        $contributors = $this->contributors;
        $top_polls_trending = $this->top_polls_trending;
        $users=$this->users;
        


        $emailSubject="What's New on Opined";
        $htmlpart=(String) view('frontend.email.digest.weekly')->with(['opinions'=>$opinions,'latest_posts'=>$latest_posts,'trending_threads'=>$trending_threads,'contributors'=>$contributors,'top_polls_trending'=>$top_polls_trending]);
        $message_body=[];
        foreach($users as $user){
            $message=[
                'From' => [
                    'Email' => "notification@weopined.com",
                    'Name' => "Opined"
                ],
                'To' => [
                    [
                        'Email' => $user->email,
                        'Name' =>$user->name
                    ]
                ],
                'ReplyTo'=>[
                    'Email'=>'no-reply@weopined.com',
                    'Name'=>'No Reply'
                ],
                'Subject' => $emailSubject,
                'HTMLPart' => $htmlpart
            ];
            array_push($message_body,$message);
        }
        $body=['Messages' =>$message_body];
        $mj = new \Mailjet\Client(getenv('MAILJET_PUBLIC_KEY'), getenv('MAILJET_SECRET_KEY'),true,['version' => 'v3.1']);
        $response = $mj->post(Resources::$Email,['body' => $body]);
        return $response->getData();

        /* Redis::throttle('zoho')->allow(5)->every(10)->then(function() use ($opinions,$latest_posts,$trending_threads,$user){
            Mail::send(new SendWeeklyDigestMail($opinions,$latest_posts,$trending_threads,$user));
                 ->onConnection('redis')
                ->onQueue('digest'));
        }, function () {
            return $this->release(5);
        }); */

    }
}
