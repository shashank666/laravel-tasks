<?php

namespace App\Http\Controllers\Frontend\Contest;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
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
use App\Model\Contest;
use App\Model\Tag;
use App\Model\Shares;
use App\Model\CategoryFollower;
use App\Model\PollType;
use App\Model\Polls;
use App\Model\PollResults;
use App\Model\Post;
use App\Model\Follower;
use App\Model\Bookmark;
use App\Model\Like;
use App\Model\PollRelation;
use App\Model\PollThread;
use App\Model\PollMultipleChoiceOption;
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


class ContestController extends Controller
{

    
    public function __construct()
    {
        $from=Carbon::now()->subDays(30);
        $to=Carbon::now();

        
        // $google_ad=DB::table('google_ads')->where(['id'=>1,'is_active'=>1])->first();
        // $google_native_ad=DB::table('google_ads')->where(['id'=>2,'is_active'=>1])->first();
        // View::share('google_ad',$google_ad);
        // View::share('google_native_ad',$google_native_ad);
        /*$this->middleware('auth',['except'=>['']]);*/
    }

    

    public function get_contest_details(Request $request){
        
    


        // $contest= Contest::where('is_active',1)->orderBy('created_at','desc')->paginate(20);

        // if($request->has('json') && $request->query('json')==1){
        //     return response()->json(array('contest'=>$contest));
        // }
        
        //var_dump($polls);
        if(Auth::check()){
            return redirect("/feed");
        }
        return view('frontend.contest.landing');
    }

    public function get_contest_detail(Request $request)
        {
    
    
             $from=Carbon::now()->subDays(60);
             $to=Carbon::now();
    
            
             //$all_categoryids=DB::table('categories')->select('id')->get()->pluck('id')->toArray();
             
             //$liked=Auth::check()?auth()->user()->likes->pluck('id')->toArray():[];
    
             if(Auth::check()){
                 $following_ids=Auth::user()->active_followings->pluck('id')->toArray();
                 $my_followed_categoryids=$this->my_followed_categoryids(Auth::user()->id);
                 $followed_category_posts=[];
                 $other_categories_posts=[];
    
                 $category_posts=CategoryPost::distinct(['id','post_id'])->where('is_active',1)->whereIn('id',$my_followed_categoryids)->orderBy('created_at','desc')->take(2)->get();
                 foreach($category_posts as $article=>$category_post){
                     $post=Post::where(['id'=>$category_post->post_id,'is_active'=>1,'status'=>1])->with('user:id,name,username,unique_id,image','categories:id,name,image','threads:id,name')->first();
                     if($post){
                         array_push($followed_category_posts,$post);
                     }
                 }
                 $other_categoryids=array_values(array_diff($all_categoryids,$my_followed_categoryids));
                 $other_category_posts_ids=CategoryPost::select('post_id')->distinct(['post_id'])->where('is_active',1)->whereIn('id',$other_categoryids)->orderBy('created_at','desc')->take(6)->get();
                 foreach($other_category_posts_ids as $article=>$other_category_post){
                     $post=Post::where(['id'=>$other_category_post->post_id,'is_active'=>1,'status'=>1])->with('user:id,name,username,unique_id,image','categories:id,name,image','threads:id,name')->first();
                     if($post){
                         if(!in_array($post,$other_categories_posts)){
                             array_push($other_categories_posts,$post);
                         }
                     }
                 }
                 $circle_latest_posts=Post::where(['status'=>1,'is_active'=> 1,'platform'=>'website'])->whereIn('user_id',$following_ids)->with('user:id,name,username,unique_id,image','categories:id,name,image','threads:id,name')->orderBy('created_at','desc')->take(2)->get();
             }else{
                 $circle_threads=[];
                 $circle_latest_posts=[];
                 $followed_category_posts=[];
                 $other_categories_posts=[];
                 $latest_threads=[];
                 $followed_threads=[];
                 $opinions=[];
                 $liked=[];
                 $liked_threads=[];
                 $followed_threadids=[];
                 $other_category_posts_ids=CategoryPost::select('post_id')->distinct(['post_id'])->where('is_active',1)->whereIn('id',$all_categoryids)->orderBy('created_at','desc')->take(12)->get();
                 foreach($other_category_posts_ids as $article=>$other_category_post){
                     $post=Post::where(['id'=>$other_category_post->post_id,'is_active'=>1,'status'=>1])->with('user:id,name,username,unique_id,image','categories:id,name,image','threads:id,name')->first();
                     if($post){
                         if(!in_array($post,$other_categories_posts)){
                             array_push($other_categories_posts,$post);
                         }
                     }
                 }
             }
    
            $trending_posts=Post::where(['status'=>1,'is_active'=>1,'platform'=>'website'])->whereBetween('created_at',[$from,$to])->with('user','categories')->orderBy('views','desc')->take(4)->get();
            $mostliked_posts=Post::where(['status'=>1,'is_active'=>1,'platform'=>'website'])->with('user','categories')->orderBy('likes','desc')->take(4)->get();
    
            $bookmarked_posts=Auth::check()?$this->my_bookmarked_postids(Auth::user()->id):[];
            $liked_posts=Auth::check()?$this->my_liked_postids(Auth::user()->id):[];
            return view('frontend.posts.show.article',compact('liked','trending_posts','mostliked_posts','circle_latest_posts','followed_category_posts','other_categories_posts','bookmarked_posts','liked_posts','liked'));
    
    
        }
        public function leaderboard_details(Request $request) {

            $thread=Thread::where(['name'=>'Bjp','is_active'=>1])->first();
    
            if($thread){
                  $thread_opinions=ThreadOpinion::where(['thread_id'=>$thread->id])->orderBy('created_at','desc')->paginate(12);
                  $writtent_opinion=ThreadOpinion::where(['thread_id'=>$thread->id,'is_active'=>1])->get();
                  $comment_opinion =  DB::table('thread_opinions')->where('thread_id',$thread->id)
                                    ->join('short_opinion_comments', 'thread_opinions.short_opinion_id', '=', 'short_opinion_comments.short_opinion_id')
                                    ->where('short_opinion_comments.is_active',1)
                                    ->whereNotIn('short_opinion_comments.status', [0])
                                    ->get();
    
                $total_opinions=count($writtent_opinion)+count($comment_opinion);
                  
    
                $trending_thread_opinions = ThreadOpinion::where(['thread_opinions.thread_id'=>$thread->id,'thread_opinions.is_active'=>1])
                    ->with('mostliked_opinion')
                    ->leftJoin('short_opinions', 'short_opinions.id', '=', 'thread_opinions.short_opinion_id')
                    ->leftJoin('short_opinion_comments', 'short_opinion_comments.short_opinion_id', '=', 'thread_opinions.short_opinion_id')
                    ->leftJoin('shares', 'shares.short_opinion_id', '=', 'thread_opinions.short_opinion_id')
                    ->leftJoin('short_opinion_likes', 'short_opinion_likes.short_opinion_id', '=', 'thread_opinions.short_opinion_id')
                    ->select('thread_opinions.*', DB::raw('(COUNT(short_opinion_comments.id)) + (COUNT(shares.id)) + (COUNT(short_opinion_likes.id)) as count'))
                    ->groupBy('thread_opinions.id')
                    ->orderBy('count','desc')
                    ->paginate(15);
                $trending_opinions=[];
                foreach($trending_thread_opinions as $trending_thread_opinion)
                  {
                    $trending_thread_opinion=ShortOpinion::where(['id'=>$trending_thread_opinion->short_opinion_id,'is_active'=>1])->with('user')->first();
                    if($trending_thread_opinion){
                        array_push($trending_opinions,$trending_thread_opinion);
                    }
                  }
                
                    return  view('frontend.contest.crud.leaderboard',compact('thread','thread_opinions','trending_opinions'));
                    
            
                // return view('frontend.contest.crud.leaderboard',compact('contest'));
           
                
        }else{
            echo "Nothing Found";
        }
    }
        
    
}
