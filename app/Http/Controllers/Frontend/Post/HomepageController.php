<?php

namespace App\Http\Controllers\Frontend\Post;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Model\Category;
use App\Model\CategoryPost;
use App\Model\CategoryThread;
use App\Model\Thread;
use App\Model\ThreadOpinion;
use App\Model\ShortOpinion;
use App\Model\ThreadFollower;
use App\Model\Post;
use App\Model\Shares;
use App\Model\User;
use App\Model\AdClick;
use App\Model\Streak;
use DB;
use Config;
use Carbon\Carbon;
use Facades\App\Repository\Homepage;
use Facades\App\Repository\Articles;



class HomepageController extends Controller
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
     
      $this->threads= Homepage::get_home_thread_opinions();
      foreach ($this->threads as $trending_thread) {
           $trending_thread->thread->opinions_count = $trending_thread->thread->opinions_count + $trending_thread->thread->comment_count;
           //var_dump($followed_thread->opinions_count);

      }
     
      //

      //
      // $google_ad=DB::table('google_ads')->where(['id'=>1,'is_active'=>1])->first();
      // $google_native_ad=DB::table('google_ads')->where(['id'=>8,'is_active'=>1])->first();

      View::share('threads',$this->threads);
      // View::share('latest_posts',$latest_posts);
      // View::share('google_ad',$google_ad);
      // View::share('google_native_ad',$google_native_ad);
      $this->middleware('auth',['only'=>['get_circle_posts','get_interested_posts']]);
    }

    public function threads_show(Request $request)
    {

         $from=Carbon::now()->subDays(60);
         $to=Carbon::now();

        
         //Cached
        //  $latest_opinions= Homepage::latest_opinions();
         //Cached
         $trending_opinions = Homepage::trending_opinions();
         
        // var_dump($trending_opinions);
         if(Auth::check()){
        $followed_threads= Homepage::followed_threads();
        foreach ($followed_threads as $followed_thread) {
             $followed_thread->thread->opinions_count = $followed_thread->thread->opinions_count + $followed_thread->thread->comment_count;
        }
        $followed_threadids= Homepage::followed_threadids();
       
        $liked=$this->get_user_liked_opinionids();
        $liked_threads=$this->get_user_liked_threadids();
        $followed_threadids=$this->get_user_followed_threadids();
        // $latest_threads =Thread::where('is_active',1)->whereNotIn('id',$followed_threadids)->withCount('comment','opinions')->has('opinions', '>', 0)->orderBy('created_at','desc')->take(6)->get();
        }
        else{
            // $latest_threads = Homepage::latest_threads();
        }
        // foreach ($latest_threads as $latest_thread) {
        //      $latest_thread->opinions_count = $latest_thread->opinions_count + $latest_thread->comment_count;
        // }
         $trending_threads= Homepage::trending_threads();
         $trending_threads_with_opinions=[];
        //  foreach($trending_threads as $threads_show=>$thread){
        //      $opinions=[];
        //      $trending_thread_opinion_ids=ThreadOpinion::select('short_opinion_id')->distinct('short_opinion_id')->where(['thread_id'=>$thread->id,'is_active'=>1])->orderBy('created_at','desc')->take(6)->pluck('short_opinion_id')->toArray();
        //      foreach($trending_thread_opinion_ids as $id){
        //          $opinion=ShortOpinion::where(['id'=>$id,'is_active'=>1])->with('user:id,name,username,unique_id,image')->first();
        //          if($opinion){
        //              array_push($opinions,$opinion);
        //          }
        //      }
        //      $thread->opinions=$opinions;
        //      array_push($trending_threads_with_opinions,$thread);
        //  }
        $opinions=[];
         $liked=Auth::check()?auth()->user()->likes->pluck('id')->toArray():[];
         $disliked=Auth::check()?auth()->user()->Disagree->pluck('id')->toArray():[];


         if(Auth::check()){
             $following_ids=Auth::user()->active_followings->pluck('id')->toArray();
             
            //$short_opinion_ids=ShortOpinion::select('id')->where('is_active',1)->whereIn('user_id',$following_ids)->get()->pluck('id')->toArray();
            //$thread_ids=ThreadOpinion::select('thread_id')->where(['is_active'=>1])->whereIn('short_opinion_id',$short_opinion_ids)->distinct('thread_id')->get()->pluck('thread_id')->toArray();
            //$circle_threads=Thread::where('is_active',1)->whereIn('id',$thread_ids)->withCount('opinions')->has('opinions', '>', 0)->orderBy('opinions_count','desc')->take(6)->get();

            $circle_threads=ThreadOpinion::where('is_active',1)
            ->whereBetween('created_at',[$from,$to])
            ->whereHas('latest_opinion',function($q) use($following_ids){
                $q->whereIn('user_id',$following_ids);
            })
            ->whereNotIn('thread_id',$followed_threadids)
            ->take(5)
            ->get();
            foreach ($circle_threads as $circle_thread) {
             $circle_thread->thread->opinions_count = $circle_thread->thread->opinions_count + $circle_thread->thread->comment_count;
             
            }
        }
        else{
             $circle_threads=[];
             $followed_threads=[];
             $opinions=[];
             $liked=[];
             $liked_threads=[];
             $followed_threadids=[];
             
         }
        // if(Auth::user()) {
        //     $this->streak_update(Auth::user()->id);
        // }
        return view('frontend.posts.show.threads_show',compact('circle_threads','trending_threads_with_opinions','liked','disliked','followed_threads','opinions','liked','liked_threads','followed_threadids','trending_opinions'));


    }

    public function get_user_liked_opinionids(){
        if(Auth::check()){
          $liked_opinions=auth()->user()->likes->pluck('id')->toArray();
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
}