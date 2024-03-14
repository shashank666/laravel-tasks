<?php

namespace App\Http\Controllers\Tester;
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
use DB;
use Config;
use Carbon\Carbon;


class HomeController extends Controller
{
   public $threads;

    public function __construct()
    {
        $post=new Post();
        $latest_posts=$post->get_latest();

////
        /*
$from=Carbon::now()->subDays(30);
        $to=Carbon::now();
        $trending_threads=ThreadOpinion::where('is_active',1)
        ->select('thread_id',DB::raw('COUNT(short_opinion_id) AS count'))
        ->whereBetween('created_at',[$from,$to])
        ->with('thread')
        ->groupBy('thread_id')
        ->having('count', '>' , 0)
        ->orderBy('count','desc')
        ->take(24)
        ->get();

        if(count($trending_threads)>0){
            $trending_threads_ids=$trending_threads->pluck('thread_id')->toArray();
            $threads=Thread::where('is_active',1)->whereNotIn('id',$trending_threads_ids)->withCount('opinions')->has('opinions','>=','2')->orderBy('opinions_count','desc')->paginate(Config::get('app.company_ui_settings')->all_threads_pagination);
        }else{
            $threads=Thread::where('is_active',1)->withCount('opinions')->has('opinions','>=','2')->orderBy('opinions_count','desc')->paginate(Config::get('app.company_ui_settings')->all_threads_pagination);
        }
        $followed_threads=Auth::check()?$this->my_followed_threadids(Auth::user()->id):[];

        if($request->ajax()){
            $view = (String) view('frontend.threads.components.threads_loop',compact('threads','followed_threads'));
            return response()->json(['html'=>$view]);
        }
        return view('frontend.threads.trending',compact('trending_threads','threads','followed_threads'));
    
        $following_ids=Auth::user()->active_followings->pluck('id')->toArray();
        $opinions_ids=ShortOpinion::whereIn('user_id',$following_ids)->where('is_active',1)->get()->pluck('id')->toArray();
        $thread_ids=ThreadOpinion::whereIn('short_opinion_id',$opinions_ids)->get()->pluck('thread_id')->toArray();

        $threads=Thread::whereIn('id',$thread_ids)->where('is_active',1)->withCount('opinions')->has('opinions','>','0')->orderBy('created_at','desc')->paginate(Config::get('app.company_ui_settings')->all_threads_pagination);
        $followed_threads=Auth::check()?$this->my_followed_threadids(Auth::user()->id):[];

        if($request->ajax()){
            $view = (String) view('frontend.threads.components.threads_loop',compact('threads','followed_threads'));
            return response()->json(['html'=>$view]);
        }
        return view('frontend.threads.circle',compact('threads','followed_threads'));
    
        $threads=ThreadFollower::where(['user_id'=>Auth::user()->id,'is_active'=>1])->with('thread')->orderBy('created_at','desc')->paginate(Config::get('app.company_ui_settings')->all_threads_pagination);
        $followed_threads=Auth::check()?$this->my_followed_threadids(Auth::user()->id):[];
        if($request->ajax()){
            $view = (String) view('frontend.threads.components.threads_loop2',compact('threads','followed_threads'));
            return response()->json(['html'=>$view]);
        }
        return view('frontend.threads.followed',compact('threads','followed_threads'));
*/




        $from=Carbon::now()->subDays(30);
        $to=Carbon::now();
        $this->threads=ThreadOpinion::where('is_active',1)
        ->select('thread_id',DB::raw('COUNT(short_opinion_id) AS count'))
        ->whereBetween('created_at',[$from,$to])
        ->with('thread')
        ->groupBy('thread_id')
        ->orderBy('count','desc')
        ->take(9)
        ->get();

        
        //

        //
        $google_ad=DB::table('google_ads')->where(['id'=>1,'is_active'=>1])->first();
        $google_native_ad=DB::table('google_ads')->where(['id'=>8,'is_active'=>1])->first();

        View::share('threads',$this->threads);
        View::share('latest_posts',$latest_posts);
        View::share('google_ad',$google_ad);
        View::share('google_native_ad',$google_native_ad);
        $this->middleware('auth',['only'=>['get_circle_posts','get_interested_posts']]);
    }


    //function to show index page with data
    public function index(Request $request)
    {
         $from=Carbon::now()->subDays(60);
         $to=Carbon::now();
         $all_categoryids=DB::table('categories')->select('id')->get()->pluck('id')->toArray();
         $latest_opinions= ShortOpinion::where(['platform'=>'website','is_active'=>1])->with('user:id,name,username,unique_id,image')->orderBy('created_at','desc')->take(6)->get();
         $trending_threads=Thread::where('is_active',1)->withCount('opinions')->has('opinions', '>', 0)->orderBy('opinions_count','desc')->take(4)->get();
         $trending_threads_with_opinions=[];
         foreach($trending_threads as $index=>$thread){
             $opinions=[];
             $trending_thread_opinion_ids=ThreadOpinion::select('short_opinion_id')->distinct('short_opinion_id')->where(['thread_id'=>$thread->id,'is_active'=>1])->orderBy('created_at','desc')->take(6)->pluck('short_opinion_id')->toArray();
             foreach($trending_thread_opinion_ids as $id){
                 $opinion=ShortOpinion::where(['id'=>$id,'is_active'=>1,'platform'=>'website'])->with('user:id,name,username,unique_id,image')->first();
                 if($opinion){
                     array_push($opinions,$opinion);
                 }
             }
             $thread->opinions=$opinions;
             array_push($trending_threads_with_opinions,$thread);
         }
         $liked=Auth::check()?auth()->user()->likes->pluck('id')->toArray():[];

         if(Auth::check()){
             $following_ids=Auth::user()->active_followings->pluck('id')->toArray();
             $my_followed_categoryids=$this->my_followed_categoryids(Auth::user()->id);
             $followed_category_posts=[];
             $other_categories_posts=[];

            $short_opinion_ids=ShortOpinion::select('id')->where('is_active',1)->whereIn('user_id',$following_ids)->get()->pluck('id')->toArray();
            $thread_ids=ThreadOpinion::select('thread_id')->where(['is_active'=>1])->whereIn('short_opinion_id',$short_opinion_ids)->distinct('thread_id')->get()->pluck('thread_id')->toArray();
            //$circle_threads=Thread::where('is_active',1)->whereIn('id',$thread_ids)->withCount('opinions')->has('opinions', '>', 0)->orderBy('opinions_count','desc')->take(6)->get();
            $circle_threads=ThreadOpinion::where('is_active',1)
            ->select('thread_id',DB::raw('COUNT(short_opinion_id) AS count'))
            ->whereBetween('created_at',[$from,$to])
            ->whereHas('latest_opinion',function($q) use($following_ids){
                $q->whereIn('user_id',$following_ids);
            })
            ->with('thread')
            ->groupBy('thread_id')
            ->orderBy('count','desc')
            ->take(6)
            ->get();

             $category_posts=CategoryPost::distinct(['category_id','post_id'])->where('is_active',1)->whereIn('category_id',$my_followed_categoryids)->orderBy('created_at','desc')->take(3)->get();
             foreach($category_posts as $index=>$category_post){
                 $post=Post::where(['id'=>$category_post->post_id,'is_active'=>1,'status'=>1])->with('user:id,name,username,unique_id,image','categories:category_id,name,image','threads:thread_id,name')->first();
                 if($post){
                     array_push($followed_category_posts,$post);
                 }
             }
             $other_categoryids=array_values(array_diff($all_categoryids,$my_followed_categoryids));
             $other_category_posts_ids=CategoryPost::select('post_id')->distinct(['post_id'])->where('is_active',1)->whereIn('category_id',$other_categoryids)->orderBy('created_at','desc')->take(12)->get();
             foreach($other_category_posts_ids as $index=>$other_category_post){
                 $post=Post::where(['id'=>$other_category_post->post_id,'is_active'=>1,'status'=>1])->with('user:id,name,username,unique_id,image','categories:category_id,name,image','threads:thread_id,name')->first();
                 if($post){
                     if(!in_array($post,$other_categories_posts)){
                         array_push($other_categories_posts,$post);
                     }
                 }
             }
             $circle_latest_posts=Post::where(['status'=>1,'is_active'=> 1,'platform'=>'website'])->whereIn('user_id',$following_ids)->with('user:id,name,username,unique_id,image','categories:category_id,name,image','threads:thread_id,name')->orderBy('created_at','desc')->take(2)->get();
         }else{
             $circle_threads=[];
             $circle_latest_posts=[];
             $followed_category_posts=[];
             $other_categories_posts=[];
             $other_category_posts_ids=CategoryPost::select('post_id')->distinct(['post_id'])->where('is_active',1)->whereIn('category_id',$all_categoryids)->orderBy('created_at','desc')->take(12)->get();
             foreach($other_category_posts_ids as $index=>$other_category_post){
                 $post=Post::where(['id'=>$other_category_post->post_id,'is_active'=>1,'status'=>1])->with('user:id,name,username,unique_id,image','categories:category_id,name,image','threads:thread_id,name')->first();
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
        return view('frontend.posts.show.index',compact('circle_threads','latest_opinions','trending_threads_with_opinions','liked','trending_posts','mostliked_posts','circle_latest_posts','followed_category_posts','other_categories_posts','bookmarked_posts','liked_posts'));

    }

    //function to get the page Thread Home

    public function home(Request $request)
    {


        //

        /*
         $from=Carbon::now()->subDays(30);
        $to=Carbon::now();
        $trending_threads=ThreadOpinion::where('is_active',1)
        ->select('thread_id',DB::raw('COUNT(short_opinion_id) AS count'))
        ->whereBetween('created_at',[$from,$to])
        ->with('thread')
        ->groupBy('thread_id')
        ->having('count', '>' , 0)
        ->orderBy('count','desc')
        ->take(24)
        ->get();

        if(count($trending_threads)>0){
            $trending_threads_ids=$trending_threads->pluck('thread_id')->toArray();
            $threads=Thread::where('is_active',1)->whereNotIn('id',$trending_threads_ids)->withCount('opinions')->has('opinions','>=','2')->orderBy('opinions_count','desc')->paginate(Config::get('app.company_ui_settings')->all_threads_pagination);
        }else{
            $threads=Thread::where('is_active',1)->withCount('opinions')->has('opinions','>=','2')->orderBy('opinions_count','desc')->paginate(Config::get('app.company_ui_settings')->all_threads_pagination);
        }
        $followed_threads=Auth::check()?$this->my_followed_threadids(Auth::user()->id):[];

        if($request->ajax()){
            $view = (String) view('frontend.threads.components.threads_loop',compact('threads','followed_threads'));
            return response()->json(['html'=>$view]);
        }
        
        */

        //
         $from=Carbon::now()->subDays(60);
         $to=Carbon::now();
         $all_categoryids=DB::table('categories')->select('id')->get()->pluck('id')->toArray();
         $latest_opinions= ShortOpinion::where(['platform'=>'website','is_active'=>1])->with('user:id,name,username,unique_id,image')->orderBy('created_at','desc')->take(20)->get();
         $latest_threads =Thread::where('is_active',1)->withCount('opinions')->has('opinions', '>', 0)->orderBy('created_at','desc')->take(6)->get();
        if(Auth::check()){
        $followed_threads=ThreadFollower::where(['user_id'=>Auth::user()->id,'is_active'=>1])->with('thread')->orderBy('created_at','desc')->take(6)->get();
        $followed_threadids=ThreadFollower::where(['user_id'=>Auth::user()->id,'is_active'=>1])->pluck('thread_id')->toArray();
        $following_userids=Auth::user()->active_followings->pluck('id')->toArray();
        array_push($following_userids,Auth::user()->id);

        $query = ShortOpinion::query();
        $query->where('is_active',1);
        $query->with(['threads','user:id,name,username,unique_id,image']);
        $query->withCount(['likes','comments']);
        $query->whereHas('threads', function ($q) use ($followed_threadids) {
            $q->whereIn('threads.id',$followed_threadids);
        })->orWhere(function($subquey) use ($following_userids){
            $subquey->whereIn('user_id',$following_userids);
        });
        $query->orderBy('created_at','desc');
        $opinions= $query->paginate(12);

        $liked=$this->get_user_liked_opinionids();
        $liked_threads=$this->get_user_liked_threadids();
        $followed_threadids=$this->get_user_followed_threadids();
        }
         $trending_threads=Thread::where('is_active',1)->withCount('opinions')->has('opinions', '>', 0)->orderBy('opinions_count','desc')->take(6)->get();
         $trending_threads_with_opinions=[];
         foreach($trending_threads as $threads_show=>$thread){
             $opinions=[];
             $trending_thread_opinion_ids=ThreadOpinion::select('short_opinion_id')->distinct('short_opinion_id')->where(['thread_id'=>$thread->id,'is_active'=>1])->orderBy('created_at','desc')->take(6)->pluck('short_opinion_id')->toArray();
             foreach($trending_thread_opinion_ids as $id){
                 $opinion=ShortOpinion::where(['id'=>$id,'is_active'=>1,'platform'=>'website'])->with('user:id,name,username,unique_id,image')->first();
                 if($opinion){
                     array_push($opinions,$opinion);
                 }
             }
             $thread->opinions=$opinions;
             array_push($trending_threads_with_opinions,$thread);
         }
         $liked=Auth::check()?auth()->user()->likes->pluck('id')->toArray():[];

         if(Auth::check()){
             $following_ids=Auth::user()->active_followings->pluck('id')->toArray();
             $my_followed_categoryids=$this->my_followed_categoryids(Auth::user()->id);
             $followed_category_posts=[];
             $other_categories_posts=[];

            $short_opinion_ids=ShortOpinion::select('id')->where('is_active',1)->whereIn('user_id',$following_ids)->get()->pluck('id')->toArray();
            $thread_ids=ThreadOpinion::select('thread_id')->where(['is_active'=>1])->whereIn('short_opinion_id',$short_opinion_ids)->distinct('thread_id')->get()->pluck('thread_id')->toArray();
            //$circle_threads=Thread::where('is_active',1)->whereIn('id',$thread_ids)->withCount('opinions')->has('opinions', '>', 0)->orderBy('opinions_count','desc')->take(6)->get();
            $circle_threads=ThreadOpinion::where('is_active',1)
            ->select('thread_id',DB::raw('COUNT(short_opinion_id) AS count'))
            ->whereBetween('created_at',[$from,$to])
            ->whereHas('latest_opinion',function($q) use($following_ids){
                $q->whereIn('user_id',$following_ids);
            })
            ->with('thread')
            ->groupBy('thread_id')
            ->orderBy('count','desc')
            ->take(5)
            ->get();

           

             $category_posts=CategoryPost::distinct(['category_id','post_id'])->where('is_active',1)->whereIn('category_id',$my_followed_categoryids)->orderBy('created_at','desc')->take(3)->get();
             foreach($category_posts as $threads_show=>$category_post){
                 $post=Post::where(['id'=>$category_post->post_id,'is_active'=>1,'status'=>1])->with('user:id,name,username,unique_id,image','categories:category_id,name,image','threads:thread_id,name')->first();
                 if($post){
                     array_push($followed_category_posts,$post);
                 }
             }
             $other_categoryids=array_values(array_diff($all_categoryids,$my_followed_categoryids));
             $other_category_posts_ids=CategoryPost::select('post_id')->distinct(['post_id'])->where('is_active',1)->whereIn('category_id',$other_categoryids)->orderBy('created_at','desc')->take(12)->get();
             foreach($other_category_posts_ids as $threads_show=>$other_category_post){
                 $post=Post::where(['id'=>$other_category_post->post_id,'is_active'=>1,'status'=>1])->with('user:id,name,username,unique_id,image','categories:category_id,name,image','threads:thread_id,name')->first();
                 if($post){
                     if(!in_array($post,$other_categories_posts)){
                         array_push($other_categories_posts,$post);
                     }
                 }
             }
             $circle_latest_posts=Post::where(['status'=>1,'is_active'=> 1,'platform'=>'website'])->whereIn('user_id',$following_ids)->with('user:id,name,username,unique_id,image','categories:category_id,name,image','threads:thread_id,name')->orderBy('created_at','desc')->take(2)->get();
         }else{
             $circle_threads=[];
             $circle_latest_posts=[];
             $followed_category_posts=[];
             $other_categories_posts=[];
             $followed_threads=[];
             $opinions=[];
             $liked=[];
             $liked_threads=[];
             $followed_threadids=[];
             $other_category_posts_ids=CategoryPost::select('post_id')->distinct(['post_id'])->where('is_active',1)->whereIn('category_id',$all_categoryids)->orderBy('created_at','desc')->take(12)->get();
             foreach($other_category_posts_ids as $threads_show=>$other_category_post){
                 $post=Post::where(['id'=>$other_category_post->post_id,'is_active'=>1,'status'=>1])->with('user:id,name,username,unique_id,image','categories:category_id,name,image','threads:thread_id,name')->first();
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
        return view('tester.home',compact('circle_threads','latest_opinions','trending_threads_with_opinions','liked','trending_posts','mostliked_posts','circle_latest_posts','followed_category_posts','other_categories_posts','bookmarked_posts','liked_posts','latest_threads','followed_threads','opinions','liked','liked_threads','followed_threadids'));


    }

// Controller for article Home Page
    public function article(Request $request)
    {


         $from=Carbon::now()->subDays(60);
         $to=Carbon::now();

        
         $all_categoryids=DB::table('categories')->select('id')->get()->pluck('id')->toArray();
         $latest_opinions= ShortOpinion::where(['platform'=>'website','is_active'=>1])->with('user:id,name,username,unique_id,image')->orderBy('created_at','desc')->take(20)->get();
         $latest_threads =Thread::where('is_active',1)->withCount('opinions')->orderBy('created_at','desc')->take(6)->get();
        if(Auth::check()){
        $followed_threads=ThreadFollower::where(['user_id'=>Auth::user()->id,'is_active'=>1])->with('thread')->orderBy('created_at','desc')->take(6)->get();
        $followed_threadids=ThreadFollower::where(['user_id'=>Auth::user()->id,'is_active'=>1])->pluck('thread_id')->toArray();
        $following_userids=Auth::user()->active_followings->pluck('id')->toArray();
        array_push($following_userids,Auth::user()->id);

        $query = ShortOpinion::query();
        $query->where('is_active',1);
        $query->with(['threads','user:id,name,username,unique_id,image']);
        $query->withCount(['likes','comments']);
        $query->whereHas('threads', function ($q) use ($followed_threadids) {
            $q->whereIn('threads.id',$followed_threadids);
        })->orWhere(function($subquey) use ($following_userids){
            $subquey->whereIn('user_id',$following_userids);
        });
        $query->orderBy('created_at','desc');
        $opinions= $query->paginate(12);

        $liked=$this->get_user_liked_opinionids();
        $liked_threads=$this->get_user_liked_threadids();
        $followed_threadids=$this->get_user_followed_threadids();
        }
         $trending_threads=Thread::where('is_active',1)->withCount('opinions')->has('opinions', '>', 0)->orderBy('opinions_count','desc')->take(6)->get();
         $trending_threads_with_opinions=[];
         foreach($trending_threads as $article=>$thread){
             $opinions=[];
             $trending_thread_opinion_ids=ThreadOpinion::select('short_opinion_id')->distinct('short_opinion_id')->where(['thread_id'=>$thread->id,'is_active'=>1])->orderBy('created_at','desc')->take(6)->pluck('short_opinion_id')->toArray();
             foreach($trending_thread_opinion_ids as $id){
                 $opinion=ShortOpinion::where(['id'=>$id,'is_active'=>1,'platform'=>'website'])->with('user:id,name,username,unique_id,image')->first();
                 if($opinion){
                     array_push($opinions,$opinion);
                 }
             }
             $thread->opinions=$opinions;
             array_push($trending_threads_with_opinions,$thread);
         }
         $liked=Auth::check()?auth()->user()->likes->pluck('id')->toArray():[];

         if(Auth::check()){
             $following_ids=Auth::user()->active_followings->pluck('id')->toArray();
             $my_followed_categoryids=$this->my_followed_categoryids(Auth::user()->id);
             $followed_category_posts=[];
             $other_categories_posts=[];

            $short_opinion_ids=ShortOpinion::select('id')->where('is_active',1)->whereIn('user_id',$following_ids)->get()->pluck('id')->toArray();
            $thread_ids=ThreadOpinion::select('thread_id')->where(['is_active'=>1])->whereIn('short_opinion_id',$short_opinion_ids)->distinct('thread_id')->get()->pluck('thread_id')->toArray();
            //$circle_threads=Thread::where('is_active',1)->whereIn('id',$thread_ids)->withCount('opinions')->has('opinions', '>', 0)->orderBy('opinions_count','desc')->take(6)->get();
            $circle_threads=ThreadOpinion::where('is_active',1)
            ->select('thread_id',DB::raw('COUNT(short_opinion_id) AS count'))
            ->whereBetween('created_at',[$from,$to])
            ->whereHas('latest_opinion',function($q) use($following_ids){
                $q->whereIn('user_id',$following_ids);
            })
            ->with('thread')
            ->groupBy('thread_id')
            ->orderBy('count','desc')
            ->take(5)
            ->get();

           

             $category_posts=CategoryPost::distinct(['category_id','post_id'])->where('is_active',1)->whereIn('category_id',$my_followed_categoryids)->orderBy('created_at','desc')->take(3)->get();
             foreach($category_posts as $article=>$category_post){
                 $post=Post::where(['id'=>$category_post->post_id,'is_active'=>1,'status'=>1])->with('user:id,name,username,unique_id,image','categories:category_id,name,image','threads:thread_id,name')->first();
                 if($post){
                     array_push($followed_category_posts,$post);
                 }
             }
             $other_categoryids=array_values(array_diff($all_categoryids,$my_followed_categoryids));
             $other_category_posts_ids=CategoryPost::select('post_id')->distinct(['post_id'])->where('is_active',1)->whereIn('category_id',$other_categoryids)->orderBy('created_at','desc')->take(12)->get();
             foreach($other_category_posts_ids as $article=>$other_category_post){
                 $post=Post::where(['id'=>$other_category_post->post_id,'is_active'=>1,'status'=>1])->with('user:id,name,username,unique_id,image','categories:category_id,name,image','threads:thread_id,name')->first();
                 if($post){
                     if(!in_array($post,$other_categories_posts)){
                         array_push($other_categories_posts,$post);
                     }
                 }
             }
             $circle_latest_posts=Post::where(['status'=>1,'is_active'=> 1,'platform'=>'website'])->whereIn('user_id',$following_ids)->with('user:id,name,username,unique_id,image','categories:category_id,name,image','threads:thread_id,name')->orderBy('created_at','desc')->take(2)->get();
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
             $other_category_posts_ids=CategoryPost::select('post_id')->distinct(['post_id'])->where('is_active',1)->whereIn('category_id',$all_categoryids)->orderBy('created_at','desc')->take(12)->get();
             foreach($other_category_posts_ids as $article=>$other_category_post){
                 $post=Post::where(['id'=>$other_category_post->post_id,'is_active'=>1,'status'=>1])->with('user:id,name,username,unique_id,image','categories:category_id,name,image','threads:thread_id,name')->first();
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
        return view('frontend.posts.show.article',compact('circle_threads','latest_opinions','trending_threads_with_opinions','liked','trending_posts','mostliked_posts','circle_latest_posts','followed_category_posts','other_categories_posts','bookmarked_posts','liked_posts','latest_threads','followed_threads','opinions','liked','liked_threads','followed_threadids'));


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


    // function to get all categories with group
    public function get_all_category_by_group(){
        $category_groups= Category::select('category_group')->distinct()->get();
        $categories_result=[];
        for($i=0;$i<count($category_groups);$i++)
        {
            $category=Category::where(['is_active'=>1,'category_group'=>$category_groups[$i]->category_group])->get();
            array_push($categories_result,array('category_group'=>$category_groups[$i]->category_group,'category_by_group'=>$category));
        }
        return view('frontend.posts.show.categories',compact('categories_result'));
    }


    // function to show latest posts
    public function get_latest_posts(Request $request){

        $posts=Post::where(['status'=>1,'is_active'=> 1,'platform'=>'website'])
        ->with('user','categories')
        ->orderBy('created_at','desc')
        ->paginate(Config::get('app.company_ui_settings')->latest_posts_pagination);

        $bookmarked_posts=Auth::check()?$this->my_bookmarked_postids(Auth::user()->id):[];
        $liked_posts=Auth::check()?$this->my_liked_postids(Auth::user()->id):[];
        if($request->ajax()){
            $view = (String) view('frontend.posts.mixcards.big_medium_card',compact('posts','bookmarked_posts','liked_posts'));
            return response()->json(['html'=>$view]);
        }else{
        return view('frontend.posts.show.latest',compact('posts','bookmarked_posts','liked_posts'));
        }
    }


    // function to show trending posts
    public function get_trending_posts(Request $request){

        $from=Carbon::now()->subDays(60);
        $to=Carbon::now();

        $posts=Post::where(['status'=>1,'is_active'=>1,'platform'=>'website'])
        ->whereBetween('created_at',[$from,$to])
        ->with('user','categories')
        ->orderBy('views','desc')
        ->paginate(Config::get('app.company_ui_settings')->trending_posts_pagination);

        $bookmarked_posts=Auth::check()?$this->my_bookmarked_postids(Auth::user()->id):[];
        $liked_posts=Auth::check()?$this->my_liked_postids(Auth::user()->id):[];

        if($request->ajax()){
            $view = (String) view('frontend.posts.components.post_three_col',compact('posts','bookmarked_posts','liked_posts'));
            return response()->json(['html'=>$view]);
        }else{
            return view('frontend.posts.show.trending',compact('posts','bookmarked_posts','liked_posts'));
        }
    }


    public function get_mostliked_posts(Request $request){

        $posts=Post::where(['status'=>1,'is_active'=>1,'platform'=>'website'])
        ->with('user','categories')
        ->orderBy('likes','desc')
        ->paginate(config('app.company_ui_settings')->trending_posts_pagination);

        $bookmarked_posts=Auth::check()?$this->my_bookmarked_postids(Auth::user()->id):[];
        $liked_posts=Auth::check()?$this->my_liked_postids(Auth::user()->id):[];
        if($request->ajax()){
            $view = (String) view('frontend.posts.components.post_three_col',compact('posts','bookmarked_posts','liked_posts'));
            return response()->json(['html'=>$view]);
        }else{
            return view('frontend.posts.show.mostliked',compact('posts','bookmarked_posts','liked_posts'));
        }
    }

    // function to get posts by category slug
    public function get_posts_by_category(Request $request,$slug){

        $category=Category::where(['slug'=>$slug,'is_active'=>1])->first();
        if($category){

            $thread_ids=CategoryThread::where(['is_active'=>1,'category_id'=>$category->id])->orderBy('created_at','desc')->get()->pluck('thread_id')->toArray();
            $category_threads=Thread::whereIn('id',$thread_ids)->where('is_active',1)->withCount('opinions')->has('opinions','>',0)->orderBy('created_at','desc')->paginate(config('app.company_ui_settings')->category_latest_threads_pagination);
            $posts=$category->latest_posts();
            $bookmarked_posts=Auth::check()?$this->my_bookmarked_postids(Auth::user()->id):[];
            $liked_posts=Auth::check()?$this->my_liked_postids(Auth::user()->id):[];
            if($request->ajax()){
                $view = (String) view('frontend.posts.mixcards.big_medium_card_sidebar',compact('posts','bookmarked_posts','liked_posts'));
                return response()->json(['html'=>$view]);
            }else{
            return view('frontend.posts.show.by_category')->with(['posts'=>$posts,'category_threads'=>$category_threads,'category'=>$category,'bookmarked_posts'=>$bookmarked_posts,'liked_posts'=>$liked_posts]);
            }
        }else{
            abort(404);
        }
    }

    public function get_circle_posts(Request $request){
        $following_ids=Auth::user()->active_followings->pluck('id')->toArray();
        $posts=Post::where(['status'=>1,'is_active'=> 1,'platform'=>'website'])->whereIn('user_id',$following_ids)->with('user:id,name,username,unique_id,image','categories:category_id,name,image','threads:thread_id,name')->orderBy('created_at','desc')->paginate(config('app.company_ui_settings')->trending_posts_pagination);

        $bookmarked_posts=Auth::check()?$this->my_bookmarked_postids(Auth::user()->id):[];
        $liked_posts=Auth::check()?$this->my_liked_postids(Auth::user()->id):[];
        if($request->ajax()){
            $view = (String) view('frontend.posts.components.post_three_col',compact('posts','bookmarked_posts','liked_posts'));
            return response()->json(['html'=>$view]);
        }else{
            return view('frontend.posts.show.circle',compact('posts','bookmarked_posts','liked_posts'));
        }
    }

    public function get_interested_posts(Request $request){
        $my_followed_categoryids=$this->my_followed_categoryids(Auth::user()->id);
        $category_posts=CategoryPost::select('post_id')->distinct('post_id')->where('is_active',1)->whereIn('category_id',$my_followed_categoryids)->orderBy('created_at','desc')->paginate(config('app.company_ui_settings')->trending_posts_pagination);
        $posts=[];
        foreach($category_posts as $index=>$category_post){
            $post=Post::where(['id'=>$category_post->post_id,'is_active'=>1,'status'=>1])->with('user:id,name,username,unique_id,image','categories:category_id,name,image','threads:thread_id,name')->first();
            if($post){
                array_push($posts,$post);
            }
        }
        $bookmarked_posts=Auth::check()?$this->my_bookmarked_postids(Auth::user()->id):[];
        $liked_posts=Auth::check()?$this->my_liked_postids(Auth::user()->id):[];
        if($request->ajax()){
            $view = (String) view('frontend.posts.components.post_three_col',compact('category_posts','posts','bookmarked_posts','liked_posts'));
            return response()->json(['html'=>$view]);
        }else{
            return view('frontend.posts.show.interested',compact('category_posts','posts','bookmarked_posts','liked_posts'));
        }
    }

}
