<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\Digest\WeeklyDigestMailJob;

use App\Model\User;
use App\Model\Post;
use App\Model\Thread;
use App\Model\ThreadOpinion;
use App\Model\ShortOpinion;
use Carbon\Carbon;
use DB;
use App\Http\Helpers\MailJetHelper;


class WeeklyDigestTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'digest:weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Task for sending weekly digest to users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $from=Carbon::now()->subDays(30);
        $to=Carbon::now();

        $opinions=ShortOpinion::where(['is_active'=>1,'type'=>'opinion'])->whereNotNull('plain_body')->orderBy('created_at','desc')->with('user')->distinct('plain_body')->take(6)->get();
        $latest_posts=Post::where(['status'=>1,'is_active'=>1])->with('user','categories')->orderBy('created_at','desc')->take(2)->get();

        $thread_ids=DB::table('thread_opinions')->where('is_active',1)->select('thread_id',DB::raw('COUNT(short_opinion_id) AS count'))->whereBetween('created_at',[$from,$to])->groupBy('thread_id')->orderBy('count','desc')->take(6)->get()->pluck('thread_id')->toArray();
        $placeholders = implode(',',array_fill(0, count($thread_ids), '?'));
        $trending_threads= Thread::where('is_active',1)->whereIn('id',$thread_ids)->withCount('opinions')->orderByRaw("field(id,{$placeholders})", $thread_ids)->get();
            foreach($trending_threads as $thread){
                $thread->opinions_count=ThreadOpinion::where(['thread_id'=>$thread->id,'is_active'=>1])->count();
            }
        $profile_user=User::where(['is_active'=>1])->first();
            if($profile_user){
              $contributors = DB::table('short_opinions')
                ->leftJoin('users', 'users.id', '=', 'short_opinions.user_id')
                ->where(['short_opinions.is_active'=>1, 'users.is_active'=>1])
                ->whereBetween('short_opinions.created_at',[Carbon::now()->subDays(7),Carbon::now()])
                ->groupBy('short_opinions.user_id')
                ->select('users.id','users.name','users.username','users.unique_id','users.is_active','users.email','users.bio','users.image', DB::raw("COUNT(short_opinions.user_id) as count_opinion"))
                ->orderBy('count_opinion', 'desc')
                ->limit(4)
                ->get();
            }
        $top_polls_trending = DB::table('poll_results')
                ->leftJoin('polls', 'polls.id', '=', 'poll_results.poll_id')
                ->where('polls.visibility',1)
                ->whereBetween('poll_results.created_at', [Carbon::now()->subDays(30), Carbon::now()])
                ->groupBy('poll_results.poll_id')
                ->select('polls.*', DB::raw("COUNT(poll_results.poll_id) as count_top_poll"))
                ->orderBy('count_top_poll','desc')
                ->limit(3)
                ->get();
        $failed_emails=DB::table('failed_emails')->get()->pluck('email')->toArray();
        User::select('id','name','email')->whereNotIn('email',$failed_emails)->where(['is_active'=>1,'is_subscribed'=>1])->orderBy('id')->chunk(20, function ($users) use($opinions,$latest_posts,$trending_threads,$contributors,$top_polls_trending){
            dispatch((new WeeklyDigestMailJob($opinions,$latest_posts,$trending_threads,$contributors,$top_polls_trending,$users))->onQueue('digest'));
            
            usleep(2000000);
        });

        

    }
}
