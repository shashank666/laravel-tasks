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




class PostsController extends Controller
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
         $disliked=Auth::check()?auth()->user()->Disagree->pluck('id')->toArray():[];


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
        return view('frontend.posts.show.index',compact('circle_threads','latest_opinions','trending_threads_with_opinions','liked','disliked','trending_posts','mostliked_posts','circle_latest_posts','followed_category_posts','other_categories_posts','bookmarked_posts','liked_posts'));

    }

    //function to get the page Thread Home

    public function threads_show(Request $request)
    {

         $from=Carbon::now()->subDays(60);
         $to=Carbon::now();

        
         //Cached
         $latest_opinions= Homepage::latest_opinions();
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
        $latest_threads =Thread::where('is_active',1)->whereNotIn('id',$followed_threadids)->withCount('comment','opinions')->has('opinions', '>', 0)->orderBy('created_at','desc')->take(6)->get();
        }
        else{
            $latest_threads = Homepage::latest_threads();
        }
        foreach ($latest_threads as $latest_thread) {
             $latest_thread->opinions_count = $latest_thread->opinions_count + $latest_thread->comment_count;
        }
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
        return view('frontend.posts.show.threads_show',compact('circle_threads','latest_opinions','trending_threads_with_opinions','liked','disliked','latest_threads','followed_threads','opinions','liked','liked_threads','followed_threadids','trending_opinions'));


    }

// Controller for article Home Page
    public function article(Request $request)
    {


         $from=Carbon::now()->subDays(60);
         $to=Carbon::now();

        
         $all_categoryids= Articles::get_all_categoryids();
         
         $liked=Auth::check()?auth()->user()->likes->pluck('id')->toArray():[];
         $disliked=Auth::check()?auth()->user()->Disagree->pluck('id')->toArray():[];


         if(Auth::check()){
             $following_ids=Auth::user()->active_followings->pluck('id')->toArray();
             $my_followed_categoryids=$this->my_followed_categoryids(Auth::user()->id);
             $followed_category_posts=[];
             $other_categories_posts=[];

             $category_posts=CategoryPost::distinct(['category_id','post_id'])->where('is_active',1)->whereIn('category_id',$my_followed_categoryids)->orderBy('created_at','desc')->take(2)->get();
             foreach($category_posts as $article=>$category_post){
                 $post=Post::where(['id'=>$category_post->post_id,'is_active'=>1,'status'=>1])->with('user:id,name,username,unique_id,image')->first();
                 if($post){
                     array_push($followed_category_posts,$post);
                 }
             }
             $other_categoryids=array_values(array_diff($all_categoryids,$my_followed_categoryids));
             $other_category_posts_ids=Articles::other_category_posts_ids($other_categoryids);
             foreach($other_category_posts_ids as $article=>$other_category_post){
                 $post=Post::where(['id'=>$other_category_post->post_id,'is_active'=>1,'status'=>1])->with('user:id,name,username,unique_id,image')->first();
                 if($post){
                     if(!in_array($post,$other_categories_posts)){
                         array_push($other_categories_posts,$post);
                     }
                 }
             }
             $circle_latest_posts=Post::where(['status'=>1,'is_active'=> 1,'platform'=>'website'])->whereIn('user_id',$following_ids)->with('user:id,name,username,unique_id,image')->orderBy('created_at','desc')->take(2)->get();
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
             $other_category_posts_ids=Articles::get_category_posts($all_categoryids);
             foreach($other_category_posts_ids as $article=>$other_category_post){
                 $post=Articles::get_other_posts($other_category_post);
                 if($post){
                     if(!in_array($post,$other_categories_posts)){
                         array_push($other_categories_posts,$post);
                     }
                 }
             }
         }

        $trending_posts= Articles::trending_posts();
        $mostliked_posts= Articles::mostliked_posts();

        $bookmarked_posts=Auth::check()?$this->my_bookmarked_postids(Auth::user()->id):[];
        $liked_posts=Auth::check()?$this->my_liked_postids(Auth::user()->id):[];
        return view('frontend.posts.show.article',compact('liked','disliked','trending_posts','mostliked_posts','circle_latest_posts','followed_category_posts','other_categories_posts','bookmarked_posts','liked_posts','liked','disliked'));


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
        /*
        if($posts){
            $trends = DB::table('likes')
            ->whereBetween('created_at',[$from,$to])
            ->leftJoin('posts', 'posts.id', '=', 'likes.post_id')
            ->where('likes.is_active',1)
            ->groupBy('likes.post_id')
            ->select('posts.id','posts.title', DB::raw("COUNT(likes.post_id) as count"))
            ->orderBy('count', 'desc')
            ->get();
        }
    */
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
        $posts=Post::where(['status'=>1,'is_active'=> 1,'platform'=>'website'])->whereIn('user_id',$following_ids)->with('user:id,name,username,unique_id,image')->orderBy('created_at','desc')->paginate(config('app.company_ui_settings')->trending_posts_pagination);

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

    public function get_user_card(Request $request){
       
        $user=User::where('id',$request->userid)->withCount('active_followers')->where(['is_active'=>1])->first();
        $ext = preg_match('/\./', $user->image) ? preg_replace('/^.*\./', '', $user->image) : '';
        $path=$user->image;
        $string="/profile";
        $substring="avatar_thumb";
        $extn = preg_match('/\./', ucfirst($user->cover_image)) ? preg_replace('/^.*\./', '', ucfirst($user->cover_image)) : '';
        if(strpos($path, $string) !== false || strpos($path,$substring) !== false){
            $dp_link = preg_replace('/.[^.]*$/', '',$user->image).'_100x100'.'.'.$ext;
        }else{
           $dp_link = $user->image;
        }

        

        $cover_img = ucfirst($user->cover_image)!==null?preg_replace('/.[^.]*$/', '',ucfirst($user->cover_image)).'_760x200'.'.'.$extn:'/storage/cover_image/cover.png';
        $html = "<div class='headiv'>";
        
            $username = $user->username;
            $name = $user->name;
           /* 
            $html .= "<span class='head'>Name : </span><span>".$name."</span><br/>";
            $html .= "<span class='head'>Username : </span><span>".$username."</span><br/>";
            */
        
        $html .= '</div>';

        $html .= '<div class="hovercard"> <div> <div class="display-pic"> <div class="cover-photo"><img src="'.$cover_img.'" style="width: 100%;"> </div><div class="profile-pic"> <div > <img class="pic" src="'.$dp_link.'" title="Profile Image"> </div><div class="details"> <ul class="details-list"> <li class="details-list-item"> <span> <span class="glyph glyph-home"></span> <span>'.$user->keywords.'<a href=""></a> <a href="#"></a></span> </span></li><li class="details-list-item">';
        if(Auth::check()){
                if($user->id==auth()->user()->id){
                   $html .= '</li></ul> </div></div></div>'; 
                }
                elseif($user->id!=auth()->user()->id){
                $followingids = auth()->user()->active_followings->pluck('id')->toArray();
                if(!in_array($user->id,$followingids)){
                    $html .=' <p> <span class="glyph glyph-work"></span> <span> <button data-userid="'.$user->id .'" class="followbtn followbtn_'.$user->id.' btn btn-sm btn-primary"><span><i class="fas fa-user-plus mr-2"></i><span>Add to Circle</button></span> </p></li></ul> </div></div></div>';
                }
                else{
                    
                    $html .=' <p> <span class="glyph glyph-work"></span> <span> <button data-userid="'.$user->id .'" class="followingbtn followingbtn_'.$user->id.' btn btn-sm btn-primary"><span><i class="fas fa-check mr-2"></i><span>Added in Circle</button></span> </p></li></ul> </div></div></div>';
                }
            }
                
            }else{
                $html .=' <p> <span class="glyph glyph-work"></span> <span> <button data-userid="'.$user->id .'" class="followbtn followbtn_'.$user->id.' btn btn-sm btn-primary"><span><i class="fas fa-user-plus mr-2"></i><span>Add to Circle</button></span> </p></li></ul> </div></div></div>';
             }

        $html .= '<div class="display-pic-gradient" style="border-radius: 0 0 0 120px;background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgb(247, 150, 7,0.20)), color-stop(100%,rgb(98, 112, 129)));"></div><div class="title-container" style = "z-index: 1;"> <a class="title" href="/@'.$username.'" title="Visit Profile" style = "cursor:pointer;">'.$name.'</a>';
        if($user->active_followers_count>0){
            $html .= '<p class="other-info">Member of '.$user->active_followers_count.' Circles</p>';
        }
        else{
            $html .= '';
        }
        $html .= '</div><!--<div class="info"> <div class="info-inner"> <div class="interactions"> <a href="#" class="btn">Add Friend</a> <a href="#" class="btn">Add in circle</a> </div></div></div></div></div>-->';

        echo $html;
    }

    public function updateUserClick(Request $request){

        $ip_address=isset($_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:$request->ip();
        $post_id = $request->input('post_id');


        $click=new AdClick();
        $click->post_id=$post_id;
        if(Auth::user()){
            $user_id = Auth::user()->id;
            $click->user_id= $user_id;
          }
        $click->ip_address= $ip_address;
        $click->clicked_at= Carbon::now();
        
        $click->save();
        if($click){
            return response()->json(array('status'=>'success','message'=>'Rsm Updated'));
        }else{
            return response()->json(array('status'=>'error','message'=>'Failed To Update Rsm'));
        }
    }

    // public function streak_update($user_id) {
    //     $streak=Streak::where(['user_id'=>$user_id])->first();
    //     $yesterdaytime=Carbon::yesterday()->timestamp;
    //     $todaytime=Carbon::today()->timestamp;
    //     $session_user=DB::table('sessions')
    //                     ->where('user_id',$user_id)
    //                     ->where('last_activity','>=',$yesterdaytime)
    //                     ->where('last_activity','<',$todaytime)
    //                     ->first();
    //     if($streak!=null) {
    //         if($session_user!=null && Carbon::now()->diffInDays($streak->updated_at)==0) {
    //             Streak::where('user_id',$user_id)->update([
    //                 'streak'=>$streak->streak+1
    //             ]);
    //             $streakCount=$streak->streak;
    //             if($streakCount>=10 && $streakCount<30) {
    //                 $achievement=UserAchievement::where(['user_id'=>Auth::user()->id, 'achievements_id'=>100])->first();
    //                 if($achievement==null) {
    //                     UserAchievement::create([
    //                         'achievements_id'=>100,
    //                         'user_id'=>Auth::user()->id
    //                     ]);
    //                     $point=Point::where(['user_id'=>Auth::user()->id])->first();
    //                     if($point==null) {
    //                         Point::create([
    //                             'user_id'=>Auth::user()->id,
    //                             'comment_points'=>0,
    //                             'reward_points'=>250,
    //                             'agree_points'=>0,
    //                             'follower_points'=>0,
    //                             'post_points'=>0,
    //                             'share_points'=>0,
    //                             'daily_points'=>250
    //                         ]);
    //                     } else {
    //                         Point::where(['user_id'=>Auth::user()->id])->update([
    //                             'reward_points'=>$point->reward_points+250,
    //                         ]);
    //                         if(Carbon::now()->diffInDays($point->updated_at)==0) {
    //                             Point::where(['user_id'=>Auth::user()->id])->update([
    //                                 'daily_points'=>$point->daily_points+250,
    //                             ]);
    //                         } else {
    //                             Point::where(['user_id'=>Auth::user()->id])->update([
    //                                 'daily_points'=>250,
    //                             ]);
    //                         }
    //                     }
    //                 }
    //             } else if($streakCount>=30 && $streakCount<90) {
    //                 $achievement=UserAchievement::where(['user_id'=>Auth::user()->id, 'achievements_id'=>101])->first();
    //                 if($achievement==null) {
    //                     UserAchievement::create([
    //                         'achievements_id'=>101,
    //                         'user_id'=>Auth::user()->id
    //                     ]);
    //                     $point=Point::where(['user_id'=>Auth::user()->id])->first();
    //                     Point::where(['user_id'=>Auth::user()->id])->update([
    //                         'reward_points'=>$point->reward_points+500
    //                     ]);
    //                     if(Carbon::now()->diffInDays($point->updated_at)==0) {
    //                         Point::where(['user_id'=>Auth::user()->id])->update([
    //                             'daily_points'=>$point->daily_points+500,
    //                         ]);
    //                     } else {
    //                         Point::where(['user_id'=>Auth::user()->id])->update([
    //                             'daily_points'=>500,
    //                         ]);
    //                     }
    //                 }
    //             } else if($streakCount>=90) {
    //                 $achievement=UserAchievement::where(['user_id'=>Auth::user()->id, 'achievements_id'=>102])->first();
    //                 if($achievement==null) {
    //                     UserAchievement::create([
    //                         'achievements_id'=>102,
    //                         'user_id'=>Auth::user()->id
    //                     ]);
    //                     $point=Point::where(['user_id'=>Auth::user()->id])->first();
    //                     Point::where(['user_id'=>Auth::user()->id])->update([
    //                         'reward_points'=>$point->reward_points+1000,
    //                     ]);
    //                     if(Carbon::now()->diffInDays($point->updated_at)==0) {
    //                         Point::where(['user_id'=>Auth::user()->id])->update([
    //                             'daily_points'=>$point->daily_points+1000,
    //                         ]);
    //                     } else {
    //                         Point::where(['user_id'=>Auth::user()->id])->update([
    //                             'daily_points'=>1000,
    //                         ]);
    //                     }
    //                 }
    //             }
    //         } else {
    //             Streak::where('user_id',$user_id)->update([
    //                 'streak'=>0
    //             ]); 
    //         }
    //     } else {
    //         if($session_user!=null) {
    //             Streak::create([
    //                 'user_id'=>$user_id,
    //                 'streak'=>1
    //             ]);
    //         } else {
    //             Streak::create([
    //                 'user_id'=>$user_id,
    //                 'streak'=>0
    //             ]); 
    //         }
    //     }
    // }
}
