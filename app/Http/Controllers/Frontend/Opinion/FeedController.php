<?php

namespace App\Http\Controllers\Frontend\Opinion;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionComment;
use App\Model\ShortOpinionLike;
use App\Model\Thread;
use App\Model\ThreadOpinion;
use App\Model\ThreadLike;
use App\Model\ThreadFollower;
use App\Model\User;
use App\Model\UserDevice;
use App\Model\OfferPost;
use App\Model\Category;
use App\Model\Tag;
use App\Model\Shares;
use App\Model\CategoryFollower;
use App\Events\OpinionViewCounterEvent;


use App\Model\Post;
use App\Model\Follower;
use App\Model\Bookmark;
use App\Model\Like;
use DB;
use App\Events\ThreadViewCounterEvent;
use Image;

use Notification;
use App\Notifications\Frontend\ShortOpinionLiked;
use App\Notifications\Frontend\ThreadLiked;
use App\Notifications\Frontend\ShortOpinionCreated;

use App\Jobs\AndroidPush\ShortOpinionLikedJob;
use App\Jobs\AndroidPush\ThreadLikedJob;
use App\Jobs\AndroidPush\ShortOpinionCreatedJob;

use \Carbon\Carbon;
use App\Http\Helpers\VideoStream;


class FeedController extends Controller
{

    public $threads;

    public function __construct()
    {
        $from=Carbon::now()->subDays(30);
        $to=Carbon::now();

        // $this->threads=ThreadOpinion::where('is_active',1)
        // ->select('thread_id',DB::raw('COUNT(short_opinion_id) AS count'))
        // ->whereBetween('created_at',[$from,$to])
        // ->with('thread')
        // ->groupBy('thread_id')
        // ->orderBy('count','desc')
        // ->take(9)
        // ->get();

        // foreach ($this->threads as $trending_thread) {
        //      $trending_thread->thread->opinions_count = $trending_thread->thread->opinions_count + $trending_thread->thread->comment_count;
        //      //var_dump($followed_thread->opinions_count);
        // }

        // $this->threads=ThreadOpinion::with('thread')
        // ->leftJoin('short_opinion_comments', 'short_opinion_comments.short_opinion_id', '=', 'thread_opinions.short_opinion_id')
        // ->whereBetween('short_opinion_comments.created_at',[$from,$to])
        // ->leftJoin('short_opinion_likes', 'short_opinion_likes.short_opinion_id', '=', 'thread_opinions.short_opinion_id')
        // ->whereBetween('short_opinion_likes.liked_at',[$from,$to])
        // ->select('thread_opinions.thread_id',DB::raw('(COUNT(short_opinion_comments.id)) + (COUNT(short_opinion_likes.id)) + COUNT(thread_opinions.short_opinion_id) AS count'))
        // ->whereBetween('thread_opinions.created_at',[$from,$to])
        // ->where('thread_opinions.is_active',1)
        // ->groupBy('thread_opinions.thread_id')
        // ->orderBy('count','desc')
        // ->take(9)
        // ->get();
        // foreach ($this->threads as $trending_thread) {
        //      $trending_thread->thread->opinions_count = $trending_thread->thread->opinions_count + $trending_thread->thread->comment_count;
        //      //var_dump($followed_thread->opinions_count);

        // }
        // $google_ad=DB::table('google_ads')->where(['id'=>1,'is_active'=>1])->first();
        // $google_native_ad=DB::table('google_ads')->where(['id'=>2,'is_active'=>1])->first();
        // View::share('google_ad',$google_ad);
        // View::share('google_native_ad',$google_native_ad);
        // View::share('threads',$this->threads);
        $this->middleware('auth',['except'=>['stream_video','get_opinions_by_thread','get_opinions_by_thread_trending','get_opinion_by_id','share_opinion_by_id','get_user_liked_opinionids','get_user_liked_threadids','get_user_followed_threadids','updateShareCount']]);
    }

    public function feed(Request $request){

        $from=Carbon::now()->subDays(30);
        $to=Carbon::now();
        $followingids = auth()->user()->active_followings->pluck('id')->toArray();
        $profile_user=User::where(['is_active'=>1])->first();
        if($profile_user){
        //   $contributors = DB::table('short_opinions')
        //     ->leftJoin('users', 'users.id', '=', 'short_opinions.user_id')
        //     ->whereNotIn('users.id',$followingids)
        //     ->where(['short_opinions.is_active'=>1])
        //     ->whereBetween('short_opinions.created_at',[$from,$to])
        //     ->groupBy('short_opinions.user_id')
        //     ->select('users.id','users.name','users.username','users.unique_id','users.is_active','users.email','users.bio','users.image', DB::raw("COUNT(short_opinions.user_id) as count_opinion"))
        //     ->orderBy('count_opinion', 'desc')
        //     ->limit(11)
        //     ->get();

          $influencers = DB::table('followers')
            ->leftJoin('users', 'users.id', '=', 'followers.leader_id')
            ->where('followers.is_active',1)
            ->whereNotIn('users.id',$followingids)
            ->where('users.id','<>','auth()->user()->id')
            ->groupBy('followers.leader_id')
            ->select('users.id','users.name','users.username','users.unique_id','users.is_active','users.email','users.bio','users.image', DB::raw("COUNT(followers.leader_id) as count_influences"))
            ->orderBy('count_influences', 'desc')
            ->limit(11)
            ->get();
        }
        // $latest_threads =Thread::where('is_active',1)->withCount('comment','opinions')->has('opinions', '>', 0)->orderBy('created_at','desc')->take(6)->get();
        // foreach ($latest_threads as $latest_thread) {
        //      $latest_thread->opinions_count = $latest_thread->opinions_count + $latest_thread->comment_count;
        // }
    
        // $followed_threads=ThreadFollower::where(['user_id'=>Auth::user()->id,'is_active'=>1])->with('thread')->orderBy('created_at','desc')->take(6)->get();
        $followed_threads=ThreadFollower::where(['user_id'=>Auth::user()->id,'thread_followers.is_active'=>1])->join('threads','threads.id','=','thread_followers.thread_id')->get();
        

        foreach ($followed_threads as $followed_thread) {
             $followed_thread->thread->opinions_count = $followed_thread->thread->opinions_count + $followed_thread->thread->comment_count;
             //var_dump($followed_thread->opinions_count);
        }
        // $followed_threads=[];
        // foreach ($followed_threads_follower as $followed_thread_follow) {
        //     //      $followed_thread->thread->opinions_count = $followed_thread->thread->opinions_count + $followed_thread->thread->comment_count;
        //     //      //var_dump($followed_thread->opinions_count);
        //     $thread=DB::table('threads')->where(['id'=>$followed_thread_follow->thread_id,'is_active'=>1])->first();
        //     array_push($followed_threads,$thread);

        // }
    
       
        $followed_threadids=ThreadFollower::where(['user_id'=>Auth::user()->id,'is_active'=>1])->pluck('thread_id')->toArray();
        $following_userids=Auth::user()->active_followings->pluck('id')->toArray();
        array_push($following_userids,Auth::user()->id);

        $following_for_opinion_userids=Auth::user()->active_followings->pluck('id')->toArray();
        $liked_ids = ShortOpinionLike::whereIn('user_id',$following_for_opinion_userids)->where(['is_active'=>1])->pluck('short_opinion_id')->toArray();
        $commented_ids = ShortOpinionComment::whereIn('user_id',$following_for_opinion_userids)->where(['is_active'=>1])->pluck('short_opinion_id')->toArray();
           
        // $query = ShortOpinion::query();
        // $query->with(['threads','user:id,name,username,unique_id,image']);
        // $query->withCount(['likes','comments']);
        // $query->whereHas('threads', function ($q) use ($followed_threadids) {
        //     $q->whereIn('threads.id',$followed_threadids)->where(['short_opinions.is_active'=>1]);
        // })->orWhere(function($subquey) use ($following_userids){
        //     $subquey->whereIn('user_id',$following_userids)->where(['short_opinions.is_active'=>1]);
        // });
        // $query->orderBy('created_at','desc');
        // $query->where(['is_active'=>1]);
        // $opinions= $query->paginate(12);
        // $time_start = microtime(true);
        $middlequery = DB::table('thread_opinions')->whereIn('thread_id',$followed_threadids)->get();   
         $query = ShortOpinion::query();
            $query->with(['user:id,name,username,unique_id,image']);
            $query->withCount(['likes','comments']);
            // $query->whereHas('threads', function ($q) use ($followed_threadids) {
            //     $q->whereIn('threads.id',$followed_threadids)->where(['short_opinions.is_active'=>1]);
            
            $query->Where(function($subquey) use ($following_userids){
                $subquey->whereIn('user_id',$following_userids)->where(['short_opinions.is_active'=>1,'short_opinions.community_id'=>0]);
            })->orWhere(function($subqueys) use ($liked_ids){
                $subqueys->whereIn('id',$liked_ids)->where(['short_opinions.is_active'=>1,'short_opinions.community_id'=>0]);
            })->orWhere(function($subques) use ($commented_ids){
                $subques->whereIn('id',$commented_ids)->where(['short_opinions.is_active'=>1, 'short_opinions.community_id'=>0]);
            });
            foreach($middlequery as $que){
                
                $query->orWhere(function($subq) use ($que){
                    $subq->where(['short_opinions.id'=>$que->short_opinion_id,'short_opinions.community_id'=>0]);
                });
            }
            
            $query->where(['is_active'=>1]);
            $query->orderBy('last_updated_at','desc');
            $opinions= $query->paginate(20);
            

            //Raw Query
           

            foreach($opinions as $op){
                event(new OpinionViewCounterEvent($op,$request->ip()));
            }
            // $time_end = microtime(true);
            // $execution_time = ($time_end - $time_start);
            // echo 'Total Execution Time: '.($execution_time*1000).'Milliseconds';



        $liked=$this->get_user_liked_opinionids();
        $disliked=$this->get_user_disliked_opinionids();
        //$liked_threads=$this->get_user_liked_threadids();
        $followed_threadids=$this->get_user_followed_threadids();
        if($request->ajax()){
          $view = (String) view('frontend.opinions.components.opinions_loop',compact('opinions','liked','disliked'));
          return response()->json(['html'=>$view]);
        }else{
            return view('frontend.opinions.show.feed',compact('influencers','followed_threads','opinions','liked','disliked','followed_threadids','followingids'));
        }
    }

    public function get_user_liked_opinionids(){
        if(Auth::check()){
          $liked_opinions=auth()->user()->likes->pluck('id')->toArray();
        }else{
          $liked_opinions=[];
        }
        return $liked_opinions;
    }
    public function get_user_disliked_opinionids(){
        if(Auth::check()){
          $liked_opinions=auth()->user()->Disagree->pluck('id')->toArray();
        }else{
          $liked_opinions=[];
        }
        return $liked_opinions;
    }

    public function get_user_liked_threadids(){
        if(Auth::check()){
          $liked_threads=auth()->user()->liked_thread->pluck('id')->toArray();
        }else{
          $liked_threads=[];
        }
        return $liked_threads;
    }

    public function get_user_followed_threadids(){
        if(Auth::check()){
          $followed_threads=auth()->user()->followed_thread->pluck('id')->toArray();
        }else{
          $followed_threads=[];
        }
        return $followed_threads;
    }


    protected function notify_followers($object,$event){
        $followers=auth()->user()->active_followers;
        $follower_ids=count($followers)>0?$followers->pluck('id')->toArray():[];

        // if($event=='ShortOpinionLiked' && $object->user && $object->user->id!==Auth::user()->id && !in_array($object->user->id,$follower_ids)){
        //     array_push($follower_ids,$object->user->id);
        //     $followers->push($object->user);
        if($event=='ShortOpinionLiked'){
            array_push($follower_ids,$object->user->id);
            $followers->push($object->user);
        }
        $fcm_tokens=UserDevice::select('gcm_token')->distinct()->whereIn('user_id',$follower_ids)->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
        try{
            if($event=='ThreadLiked'){
                foreach(array_chunk($fcm_tokens,100) as $chunk){
                    dispatch(new ThreadLikedJob($object,Auth::user(),$chunk));
                }
                foreach($followers as $follower){
                    Notification::send($follower,new ThreadLiked($object,Auth::user(),$fcm_tokens));
                }
            }else if($event=='ShortOpinionCreated'){
                foreach(array_chunk($fcm_tokens,100) as $chunk){
                    dispatch(new ShortOpinionCreatedJob($object,Auth::user(),$chunk));
                }
                foreach($followers as $follower){
                    Notification::send($follower,new ShortOpinionCreated($object,Auth::user(),$fcm_tokens));
                }
            }else{
                foreach(array_chunk($fcm_tokens,100) as $chunk){
                    dispatch(new ShortOpinionLikedJob($object,Auth::user(),$chunk));
                }
                foreach($followers as $follower){
                    Notification::send($follower,new ShortOpinionLiked($object,Auth::user(),$fcm_tokens));
                }
            }
        }catch(\Exception $e){}

    }


}