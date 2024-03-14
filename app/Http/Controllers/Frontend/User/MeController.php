<?php

namespace App\Http\Controllers\Frontend\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Model\User;
use App\Model\UserDevice;
use App\Model\UserAccount;
use App\Model\Category;
use App\Model\CategoryFollower;
use App\Model\Tag;
use App\Model\Post;
use App\Model\OfferPost;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionLike;
use App\Model\Follower;
use App\Model\Bookmark;
use App\Model\Like;
use App\Model\Views;
use App\Model\RsmOffer;
use App\Model\RsmUserPost;
use App\Model\UserEarning;
use App\Model\Monetisation;
use App\Model\UserInvoice;
use Notification;
use App\Notifications\Frontend\UserFollowed;
use App\Notifications\Frontend\PostLiked;
use App\Jobs\AndroidPush\UserFollowedJob;
use App\Jobs\AndroidPush\PostLikedJob;

use DB;
use Config;
use Session;
use Carbon\Carbon;
use Mail;
use App\Mail\OfferPost\PostEligibleMail;

class MeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $google_ad=DB::table('google_ads')->where(['id'=>5,'is_active'=>1])->first();
        View::share('google_ad',$google_ad);
    }


    // function for show loggedin user's Articles corner page
    public function article_corner(){
        $monetisation = Monetisation::where(['user_id'=>Auth::user()->id, 'is_monetised'=>1])->get();
        return view('frontend.posts.show.mycorner',compact('monetisation'));
    }

    // function for show loggedin user's published posts
    public function get_my_posts(){
        $posts=ShortOpinion::where(['is_active'=>1,'user_id'=>Auth::user()->id])->with('user')->orderBy('created_at','desc')->paginate(Config::get('app.company_ui_settings')->my_posts_pagination);
        $liked=auth()->user()->likes->pluck('id')->toArray();
        $liked=auth()->user()->likes->pluck('id')->toArray();
        return view('frontend.opinions.show.my',compact('posts','liked','disliked'));
    }

    // function for show loggedin user's draft get_my_drafts
    public function get_my_drafts(){
        $posts=Post::where(['status' => 0,'is_active'=>1,'user_id'=>Auth::user()->id])
           //->where(function ($query) {$query->where('status', '=', 0)
             //->orWhere('status', '=', 2);})
           ->with('categories','user')->orderBy('created_at','desc')->paginate(Config::get('app.company_ui_settings')->my_drafts_pagination);
        return view('frontend.posts.show.drafts',compact('posts'));
    }
    /*
     public function get_my_drafts(){
        $posts=Post::where(['is_active'=>1,'user_id'=>Auth::user()->id])
           ->where(function ($query) {$query->where('status', '=', 0)
             ->orWhere('status', '=', 2);})
           ->with('categories','user')->orderBy('created_at','desc')->paginate(Config::get('app.company_ui_settings')->my_drafts_pagination);
        return view('frontend.posts.show.drafts',compact('posts'));
    }
    
    public function get_my_previewed(){
        $posts=Post::where(['status'=>2,'is_active'=>1,'user_id'=>Auth::user()->id])->with('categories','user')->orderBy('created_at','desc')->paginate(Config::get('app.company_ui_settings')->my_posts_pagination);
        return view('frontend.posts.show.myallarticles',compact('posts'));
    }   

    public function get_my_published(){
        $posts=Post::where(['status'=>1,'is_active'=>1,'user_id'=>Auth::user()->id])->with('categories','user')->orderBy('created_at','desc')->paginate(Config::get('app.company_ui_settings')->my_posts_pagination);
        return view('frontend.posts.show.myallarticles',compact('posts'));
    } 
    */
    public function get_my_all_articles(){
        $posts=Post::where(['is_active'=>1,'user_id'=>Auth::user()->id])
           ->where(function ($query) {$query->where('status', '=', 0)
             ->orWhere('status', '=', 2)
             ->orWhere('status', '=', 1);})
           ->with('categories','user')->orderBy('updated_at','desc')->paginate(Config::get('app.company_ui_settings')->my_drafts_pagination);
        return view('frontend.posts.show.myallarticles',compact('posts'));
    } 

     // function for get logged in user's followers
    public function get_followers(Request $request){
        //$users=auth()->user()->followers;
        $followingids= auth()->user()->active_followings->pluck('id')->toArray();

        $users = DB::table('followers')
        ->leftJoin('users', 'users.id', '=', 'followers.follower_id')
        ->where('followers.leader_id', '=',auth()->user()->id)
        ->where('followers.is_active',1)
        ->select('users.id','users.name','users.username','users.unique_id','users.email','users.image','users.bio')
        ->paginate(9);
        if($request->ajax()){
            $view = (String) view('frontend.profile.components.usersloop_three_col',compact('users','followingids'));
            return response()->json(['html'=>$view]);
        }else{
            return view('frontend.profile.index',compact('users','followingids'));
        }
    }

    // function for get logged in user's followings
    public function get_following(Request $request){
        //$users = auth()->user()->followings;
        $users = DB::table('followers')
        ->leftJoin('users', 'users.id', '=', 'followers.leader_id')
        ->where('followers.follower_id', '=',auth()->user()->id)
        ->where('followers.is_active',1)
        ->select('users.id','users.name','users.username','users.unique_id','users.email','users.image','users.bio')
        ->paginate(9);
        if($request->ajax()){
            $view = (String) view('frontend.profile.components.usersloop_three_col',compact('users'));
            return response()->json(['html'=>$view]);
        }else{
            return view('frontend.profile.index',compact('users'));
        }
    }

    // function for follow or unfollow user
    public function manage_follow(Request $request)
    {
        $userid=$request->input('userid');
        $userFound=User::find($userid);
        $userAlreadyFollows=Follower::where(['follower_id'=>Auth::user()->id,'leader_id'=>$userid])->first();
        if($userFound){
            if($userAlreadyFollows){
                Auth::user()->followings()->detach($userid);
                DB::table('notifications')
                ->where('notifiable_id',$userFound->id)
                ->where('data','like','%"event":"ADDED_IN_CIRCLE"%')
                ->where('data','like','%"follower_id":'.Auth::user()->id.'%')
                ->delete();
                $response=array('status'=>'unfollowed');
                return response()->json($response);
            }else{
                Auth::user()->followings()->attach($userid);
                try{
                    $users_fcm_tokens=UserDevice::select('gcm_token')->distinct()->whereIn('user_id',[$userFound->id])->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
                    dispatch(new UserFollowedJob(Auth::user(),$users_fcm_tokens));
                    Notification::send($userFound,new UserFollowed(Auth::user(),$users_fcm_tokens));
                }catch(\Exception $e){}
                $response=array('status'=>'followed');
                return response()->json($response);
           }
        }else{
            $response=array('status'=>'error','message'=>'User Not Found');
            return response()->json($response);
        }
    }


    // function for follow or unfollow category
    public function manage_category_follow(Request $request){
        $categoryid=$request->input('categoryid');
        $categoryFound=Category::find($categoryid);
        $categoryAlreadyFollows=CategoryFollower::where(['user_id'=>Auth::user()->id,'category_id'=>$categoryid])->first();
        if($categoryFound){
            if($categoryAlreadyFollows){
                Auth::user()->follow_category()->detach($categoryid);
                $response=array('status'=>'unfollowed');
                return response()->json($response);
            }else{
                Auth::user()->follow_category()->attach($categoryid);
                $response=array('status'=>'followed');
                return response()->json($response);
           }
        }else{
            $response=array('status'=>'error','message'=>'Category Not Found');
            return response()->json($response);
        }
    }

    // function for get logged in users followed category
    public function get_followed_category(){
        return auth()->user()->followed_categories->pluck('category_id')->toArray();
    }



    // function for like and unlike post by userid
    public function manage_likes(Request $request)
    {
        $postid=$request->input('postid');
        $post=Post::where(['id'=>$postid,'is_active'=>1])->with('user')->first();
        $rsm_offer=RsmOffer::where('user_id',$post->user['id'])->get();
        $offer_data = RsmOffer::count();
        //$offer_posts=OfferPost::where('user_id',$post->user['id'])->get();
        $status='';

        $likeFound=Like::where(['user_id'=>Auth::user()->id,'post_id'=>$postid])->first();
        if($likeFound){
            DB::table('likes')->where(['post_id'=>$postid,'user_id'=>Auth::user()->id])->delete();
            $post->likes=$post->likes-1;
            $post->save();
            DB::table('notifications')
            ->where('data','like','%"event":"ARTICLE_LIKED"%')
            ->where('data','like','%"post_id":'.$postid.'%')
            ->where('data','like','%"sender_id":'.Auth::user()->id.'%')
            ->delete();
            DB::table('fake_likes')->where(['post_id'=>$postid,'user_id'=>Auth::user()->id])->delete();

            $status='like';
            $count=DB::table('likes')->where(['post_id'=>$postid])->count();
            $response=array('status'=>$status,'count'=>$count);
            return response()->json($response);
        }else{

            if(Auth::user()->is_active && Auth::user()->email_verified==1){
                $post->increment('likes');
                DB::table('likes')->insert(['post_id'=>$postid,'user_id'=>Auth::user()->id,'ip_address'=>$request->ip(),'user_agent'=>$request->header("user-agent")]);
                $this->notify_followers($post,'PostLiked');
                $status='liked';
                //var_dump(strtotime($post->created_at));
                //var_dump(strtotime(Carbon::createSafe(2020, 3, 05, 0, 0, 0)));
                if($post->likes>=50 && $offer_data<=100 && count($rsm_offer)<1 && !in_array($post->id,$rsm_offer->pluck('post_id')->toArray()) && $post->is_active==1 && $post->status==1 && $post->user['registered_as_writer']==1 && strtotime(Carbon::createSafe(2020, 3, 05, 0, 0, 0))<=strtotime($post->created_at)){
                $eligible_post=new RsmOffer();
                $eligible_post->post_id=$post->id;
                $eligible_post->user_id=$post->user['id'];
                $eligible_post->save();
                    $check_offer=UserEarning::where(['user_id'=>$post->user['id']])->first();
                    if($check_offer){
                        $sum_earning = $check_offer->total_earning + 10;
                        UserEarning::where(['user_id'=>$post->user['id']])->update(['total_earning'=>$sum_earning]);
                        $check_offer_post = RsmUserPost::where(['post_id'=>$post->id])->first();
                        if($check_offer_post){
                            $sum_earning_post = $check_offer_post->money + 10;
                            RsmUserPost::where(['post_id'=>$post->id])->update(['money'=>$sum_earning_post]);
                        }
                        else{
                            $offer_added_post=new RsmUserPost();
                            $offer_added_post->post_id=$post->id;
                            $offer_added_post->user_id=$post->user['id'];
                            $offer_added_post->money=10;
                            $offer_added_post->save();
                        }
                    }
                    else{
                        $offer_added=new UserEarning();
                        $offer_added->user_id=$post->user['id'];
                        $offer_added->total_earning=10;
                        $offer_added->save();
                        $check_offer_post = RsmUserPost::where(['post_id'=>$post->id])->first();
                        if($check_offer_post){
                            $sum_earning_post = $check_offer_post->money + 10;
                            RsmUserPost::where(['post_id'=>$post->id])->update(['money'=>$sum_earning_post]);
                        }
                        else{
                            $offer_added_post=new RsmUserPost();
                            $offer_added_post->post_id=$post->id;
                            $offer_added_post->user_id=$post->user['id'];
                            $offer_added_post->money=10;
                            $offer_added_post->save();
                            }
                        }
                //$this->send_email_for_post_eligible($post,$post->user);
             }
            /*  OFFER CRITERIA CONDITION
            if($post->likes>=50 && count($offer_posts)<=2 && !in_array($post->id,$offer_posts->pluck('post_id')->toArray()) && $post->is_active==1 && $post->status==1 && $post->user['registered_as_writer']==1 && str_word_count($post->plainbody)>=400 && $post->plagiarism_checked==1 && $post->is_plagiarized==0){
                $eligible_post=new OfferPost();
                $eligible_post->offer_id=1;
                $eligible_post->post_id=$post->id;
                $eligible_post->user_id=$post->user['id'];
                $eligible_post->save();
                $this->send_email_for_post_eligible($post,$post->user);
             } */

             $count=DB::table('likes')->where(['post_id'=>$postid,'is_active'=>1])->count();
             $response=array('status'=>$status,'count'=>$count);
             return response()->json($response);
            }
        }
    }

    /* FUNCTION TO SEND EMAIL FOR POST ELIGIBLE FOR OFFER

    protected function send_email_for_post_eligible(Post $post,User $user){
        try {
           Mail::send(new PostEligibleMail($user,$post));
        } catch (Exception $e) {}
    }

    */


    protected function notify_followers($post,$event){

        $followers=auth()->user()->active_followers;
        $follower_ids=count($followers)>0?$followers->pluck('id')->toArray():[];

        if($post->user && $post->user->id!==Auth::user()->id && !in_array($post->user->id,$follower_ids)){
            array_push($follower_ids,$post->user->id);
            $followers->push($post->user);
        }

        $fcm_tokens=UserDevice::select('gcm_token')->distinct()->whereIn('user_id',$follower_ids)->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
       try{
            foreach(array_chunk($fcm_tokens,100) as $chunk){
                dispatch(new PostLikedJob($post,Auth::user(),$chunk));
            }
            foreach($followers as $follower){
                Notification::send($follower,new PostLiked($post,Auth::user(),$fcm_tokens));
            }
        }catch(\Exception $e){}
    }


    // function for adding and removing bookmarks by userid , postid
    public function manage_bookmark(Request $request)
    {
        $postid=$request->input('postid');
        $bookmarkFound=Bookmark::where(['user_id'=>Auth::user()->id,'post_id'=>$postid])->first();
        if($bookmarkFound){
            Auth::user()->bookmarks()->detach($postid);
            $response=array('status'=>'removed');
            return response()->json($response);
        }else{
            Auth::user()->bookmarks()->attach($postid);
            $response=array('status'=>'added');
            return response()->json($response);
        }
    }

    // function for get logged in user's bookmarks
    public function get_bookmarks(){

            //$posts=auth()->user()->bookmarks;
            $posts=Bookmark::where('bookmarks.user_id', '=',Auth::user()->id)
            ->with('post')
            ->orderBy('bookmarked_at','desc')
            ->paginate(Config::get('app.company_ui_settings')->my_bookmarked_posts_pagination);

        return view('frontend.posts.show.bookmarks',compact('posts'));
    }


    public function stats(Request $request){
        $user_id=auth()->user()->id;
        $posts=Post::where(['status'=>1,'is_active'=>1,'user_id'=>$user_id])->with('categories','user')->orderBy('created_at','desc')->paginate(12);
        $from_date=Carbon::now()->subDays(30);
        $to_date=Carbon::now();
        $dates = [];
        for($date = $from_date; $date->lte($to_date); $date->addDay()) {
            array_push($dates,array('date'=>$date->format('d F Y'),'likes'=>0,'views'=>0));
        }
        return view('frontend.profile.stats',compact('posts','dates'));
    }

    public function articlePerformance(Request $request){

        $user_id=auth()->user()->id;
        $posts=RsmUserPost::where(['user_id'=>$user_id])->with('post','user')->orderBy('created_at','desc')->paginate(12);

        $user_earning=UserEarning::where(['user_id'=>$user_id])->first();
        $monetisation = Monetisation::where(['user_id'=>$user_id])->with('post','user')->first();
        $from_date=Carbon::now()->subDays(30);
        $to_date=Carbon::now();
        $dates = [];
        for($date = $from_date; $date->lte($to_date); $date->addDay()) {
            array_push($dates,array('date'=>$date->format('d F Y'),'likes'=>0,'views'=>0));
        }
        return view('frontend.profile.article_performance',compact('posts','dates','user_earning','monetisation'));
    }

    public function invoice(Request $request){

        $user_id=auth()->user()->id;
        $posts=RsmUserPost::where(['user_id'=>$user_id])->orderBy('created_at','desc')->paginate(12);
        $user_earning=UserEarning::where(['user_id'=>$user_id])->first();
        $user_invoices=UserInvoice::where(['is_active'=>1,'user_id'=>$user_id])->with('user','user_earning')->orderBy('created_at','desc')->paginate(50);
        if($user_invoices && $user_earning){
         if($request->has('json') && $request->query('json')==1){
            return response()->json($user_invoices);
        }
         return view('frontend.profile.invoices',compact('user_invoices','user_earning'));
         }
         else{
            return redirect()->route('article_corner');
         }
    }

    public function individualInvoice(Request $request,$billing_id){
        $user_invoice=UserInvoice::where(['billing_id'=>$billing_id])->with('user','user_account')->first();
        
         if($request->has('json') && $request->query('json')==1){
            return response()->json();
        }
         return view('frontend.profile.components.individual_invoice',compact('user_invoice'));
    }
    

    public function get_post_stats(Request $request){
        $post_id=$request->input('post_id');
        $fromDate=Carbon::createFromFormat('Y-m-d',$request->input('from'));
        $toDate= Carbon::createFromFormat('Y-m-d',$request->input('to'));

        $post=Post::where(['id'=>$post_id,'user_id'=>Auth::user()->id,'is_active'=>1])->first();
        if($post){
            $post_views=
            DB::table('views')
            ->where('post_id',$post_id)
            ->select(DB::raw('DATE_FORMAT(viewed_at,"%d %M %Y") as viewed_at'),DB::raw("(COUNT(*)) as total"))
            ->orderBy('viewed_at','desc')
            ->whereBetween('viewed_at',[$fromDate,$toDate])
            ->groupBy(DB::raw('Date(viewed_at)'))
            ->get();

            $post_likes=
            DB::table('likes')
            ->where('post_id',$post_id)
            ->select(DB::raw('DATE_FORMAT(liked_at,"%d %M %Y") as liked_at'),DB::raw("(COUNT(*)) as total"))
            ->orderBy('liked_at','desc')
            ->whereBetween('liked_at',[$fromDate,$toDate])
            ->groupBy(DB::raw('Date(liked_at)'))
            ->get();

            $dates = [];
            for($date = $fromDate; $date->lte($toDate); $date->addDay()) {
                array_push($dates,array('date'=>$date->format('d F Y'),'likes'=>0,'views'=>0));
            }


            foreach($dates as $index=>$date){
                foreach($post_likes as $post){
                   $post=get_object_vars($post);
                   if(strcmp($date['date'],$post['liked_at'])===0){
                        $dates[$index]['likes']=intval($post['total']);
                   }
                }

                foreach($post_views as $post){
                    $post=get_object_vars($post);
                    if(strcmp($date['date'],$post['viewed_at'])===0){
                         $dates[$index]['views']=intval($post['total']);
                    }
                 }
            }
            $array_dates=array_pluck($dates,'date');
            $array_likes=array_pluck($dates,'likes');
            $array_views=array_pluck($dates,'views');

            return response()->json(array('status'=>'success',
            'post_id'=>$post_id,
            'from'=>$request->input('from'),
            'to'=>$request->input('to'),
            'dates'=>$array_dates,
            'views'=>$array_views,
            'likes'=>$array_likes));
        }else{
            return response()->json(array('status'=>'error','message'=>'post not found'));
        }

    }

}
