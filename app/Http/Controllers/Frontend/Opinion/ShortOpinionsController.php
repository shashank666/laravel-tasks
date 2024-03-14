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


class ShortOpinionsController extends Controller
{

    public $threads;

    public function __construct()
    {
        $from=Carbon::now()->subDays(30);
        $to=Carbon::now();

        
        $this->threads=ThreadOpinion::where('thread_opinions.is_active',1)
        ->select('thread_id',DB::raw('COUNT(short_opinion_id) AS count'))
        ->whereBetween('thread_opinions.created_at',[$from,$to])
        ->join('threads','threads.id','=','thread_opinions.thread_id')
        ->groupBy('thread_id')
        ->orderBy('count','desc')
        ->take(9)
        ->get();

        foreach ($this->threads as $trending_thread) {
             $trending_thread->thread->opinions_count = $trending_thread->thread->opinions_count + $trending_thread->thread->comment_count;
             //var_dump($followed_thread->opinions_count);
        }
        
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

        $google_ad=DB::table('google_ads')->where(['id'=>1,'is_active'=>1])->first();
        $google_native_ad=DB::table('google_ads')->where(['id'=>2,'is_active'=>1])->first();
        View::share('google_ad',$google_ad);
        View::share('google_native_ad',$google_native_ad);
        View::share('threads',$this->threads);
        $this->middleware('auth',['except'=>['stream_video','get_opinions_by_thread','get_opinions_by_thread_trending','get_opinion_by_id','share_opinion_by_id','get_user_liked_opinionids','get_user_liked_threadids','get_user_followed_threadids','updateShareCount']]);
    }

    public function stream_video(Request $request,$video_name){
        try{
           $videosDir = base_path('storage/app/public/videos');
           if(Storage::exists('public/videos/'.$video_name)) {
               $stream = new VideoStream($videosDir.'/'.$video_name);
               $stream->start();
           }else{
               $response=array('status'=>'error','result'=>0,'errors'=>'video not found');
               return response()->json($response, 404);
           }
         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }


    public function get_opinions_by_thread(Request $request,$name)
    {

        $thread=Thread::where(['name'=>$name,'is_active'=>1])->first();
        /* $eligible_count=OfferPost::count();
        $remaining_count=100-$eligible_count; */
        
        //var_dump($trending_opinions);
        

        if($thread){
              event(new ThreadViewCounterEvent($thread));
              $thread_opinions=ThreadOpinion::where(['thread_id'=>$thread->id])->orderBy('created_at','desc')->paginate(12);
              $writtent_opinion=ThreadOpinion::where(['thread_id'=>$thread->id])->where('is_active',1)->get();
              $comment_opinion =  DB::table('thread_opinions')->where('thread_id',$thread->id)
                                ->join('short_opinion_comments', 'thread_opinions.short_opinion_id', '=', 'short_opinion_comments.short_opinion_id')
                                ->where('short_opinion_comments.is_active',1)
                                ->whereNotIn('short_opinion_comments.status', [0])
                                ->get();

            $total_opinions=count($writtent_opinion)+count($comment_opinion);
              
           
            
              $opinions=[];
              foreach($thread_opinions as $opinion)
              {
                $opinion=ShortOpinion::where(['id'=>$opinion->short_opinion_id,'is_active'=>1,'community_id'=>0])->with('user')->first();
                if($opinion){
                    event(new OpinionViewCounterEvent($opinion,$request->ip()));
                    array_push($opinions,$opinion);
                }
              }
              $liked=$this->get_user_liked_opinionids();
              $disliked=$this->get_user_disliked_opinionids();
              $disliked=$this->get_user_disliked_opinionids();
              //Might Add Here
              $liked_threads=$this->get_user_liked_threadids();
              $followed_threads=$this->get_user_followed_threadids();
              
  
              

              if($request->ajax()){
                $view = (String) view('frontend.opinions.components.opinions_loop',compact('thread','thread_opinions','opinions','liked','disliked'));
                return response()->json(['html'=>$view]);
            }else{
              return view('frontend.opinions.show.by_thread',compact('thread','thread_opinions','opinions','liked','disliked','disliked','liked_threads','followed_threads','total_opinions'));
            }
        }else{
            abort(404);
        }
    }

    public function get_opinions_by_thread_trending(Request $request,$name)
    {

        $thread=Thread::where(['name'=>$name,'is_active'=>1])->first();
        /* $eligible_count=OfferPost::count();
        $remaining_count=100-$eligible_count; */
        
        //var_dump($trending_opinions);
        

        if($thread){
              $from=Carbon::now()->subDays(60);
              $to=Carbon::now();
              event(new ThreadViewCounterEvent($thread));
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
                ->whereBetween('short_opinions.created_at',[$from,$to])
                ->select('thread_opinions.*', DB::raw('(COUNT(short_opinion_comments.id)) + (COUNT(shares.id)) + (COUNT(short_opinion_likes.id)) as count'))
                ->groupBy('thread_opinions.id')
                ->orderBy('count','desc')
                ->paginate(15);


            //$trending_thread_opinions = ThreadOpinion::where(['thread_id'=>$thread->id,'is_active'=>1])->with('mostliked_opinion')->withCount('likes')->orderBy('likes_count','desc')->paginate(8);

                /* 
                $trending_opinions = ShortOpinion::where('short_opinions.is_active',1)
              ->with('user')
              ->leftJoin('thread_opinions', 'thread_opinions.short_opinion_id', '=', 'short_opinions.id')
              ->leftJoin('short_opinion_comments', 'short_opinion_comments.short_opinion_id', '=', 'short_opinions.id')
              ->leftJoin('shares', 'shares.short_opinion_id', '=', 'short_opinions.id')
              ->leftJoin('short_opinion_likes', 'short_opinion_likes.short_opinion_id', '=', 'short_opinions.id')
              ->whereBetween('short_opinions.created_at',[$from,$to])
              ->whereBetween('short_opinions.created_at',[$from,$to])
                ->select('short_opinions.*', DB::raw('(COUNT(short_opinion_comments.id)) + (COUNT(shares.id)) + (COUNT(short_opinion_likes.id)) as count'))
                ->groupBy('short_opinions.id')
                ->orderBy('count','desc')
              ->paginate(12);
                */
            $trending_opinions=[];
            foreach($trending_thread_opinions as $trending_thread_opinion)
              {
                $trending_thread_opinion=ShortOpinion::where(['id'=>$trending_thread_opinion->short_opinion_id,'is_active'=>1,'community_id'=>0])->with('user')->first();
                if($trending_thread_opinion){
                    event(new OpinionViewCounterEvent($trending_thread_opinion,$request->ip()));
                    array_push($trending_opinions,$trending_thread_opinion);
                }
              }
              
            //var_dump($trending_opinions);
              
              
                //$this->remove_null($trending_opinions);
                //var_dump($trending_opinions);
              $liked=$this->get_user_liked_opinionids();
              $disliked=$this->get_user_disliked_opinionids();
              $liked_threads=$this->get_user_liked_threadids();
              $followed_threads=$this->get_user_followed_threadids();
              
              

              if($request->ajax()){
                $view = (String) view('frontend.opinions.components.opinions_loop',compact('thread','thread_opinions','opinions','liked','disliked','trending_opinions'));
                return response()->json(['html'=>$view]);
            }else{
              return view('frontend.opinions.show.by_thread_trending',compact('thread','thread_opinions','liked','disliked','liked_threads','followed_threads','total_opinions','trending_opinions'));
            }
        }else{
            abort(404);
        }
    }

    public function get_opinions_by_thread_circle(Request $request,$name)
    {

        $thread=Thread::where(['name'=>$name,'is_active'=>1])->first();
        /* $eligible_count=OfferPost::count();
        $remaining_count=100-$eligible_count; */
        
        //var_dump($trending_opinions);
        

        if($thread){
              event(new ThreadViewCounterEvent($thread));
              $thread_opinions=ThreadOpinion::where(['thread_id'=>$thread->id])->orderBy('created_at','desc')->paginate(12);
              $writtent_opinion=ThreadOpinion::where(['thread_id'=>$thread->id])->where('is_active',1)->get();
              $comment_opinion =  DB::table('thread_opinions')->where('thread_id',$thread->id)
                                ->join('short_opinion_comments', 'thread_opinions.short_opinion_id', '=', 'short_opinion_comments.short_opinion_id')
                                ->where('short_opinion_comments.is_active',1)
                                ->whereNotIn('short_opinion_comments.status', [0])
                                ->get();

            $total_opinions=count($writtent_opinion)+count($comment_opinion);
              
              $liked=$this->get_user_liked_opinionids();
              $disliked=$this->get_user_disliked_opinionids();
              $liked_threads=$this->get_user_liked_threadids();
              $followed_threads=$this->get_user_followed_threadids();
              
              if(Auth::check()){
                        $following_ids = auth()->user()->active_followings->pluck('id')->toArray();
                        $short_opinion_ids=ShortOpinion::select('id')
                ->where(['is_active'=>1,'community_id'=>0])
                ->whereIn('user_id',$following_ids)
                ->get()->pluck('id')->toArray();

                $this->remove_null($thread);
                $circle_thread_opinions=ThreadOpinion::where(['thread_id'=>$thread->id,'is_active'=>1])
                ->with('latest_opinion')->whereIn('short_opinion_id',$short_opinion_ids)
                ->orderBy('created_at','desc')->paginate(20);
                    $circle_opinions=[];
                          foreach($circle_thread_opinions as $circle_thread_opinion)
                          {
                            $circle_thread_opinion=ShortOpinion::where(['id'=>$circle_thread_opinion->short_opinion_id,'is_active'=>1,'community_id'=>0])->with('user')->first();
                            if($circle_thread_opinion){
                                event(new OpinionViewCounterEvent($circle_thread_opinion,$request->ip()));
                                array_push($circle_opinions,$circle_thread_opinion);
                            }
                          }
                    }else{
                        $followingids =[];
                        $profile_user =[];
                        $circle_opinions = [];
                    }
                    //var_dump($circle_opinions);
                    //var_dump($followingids);
                //$following_ids=Auth::user()->user->active_followings->pluck('id')->toArray();


              if($request->ajax()){
                $view = (String) view('frontend.opinions.components.opinions_loop',compact('thread','thread_opinions','opinions','liked','disliked','trending_opinions'));
                return response()->json(['html'=>$view]);
            }else{
              return view('frontend.opinions.show.by_thread_circle',compact('thread','thread_opinions','liked','disliked','liked_threads','followed_threads','total_opinions','circle_opinions'));
            }
        }else{
            abort(404);
        }
    }

   

    public function share_opinion_by_id(Request $request,$username,$id){
        $opinion=ShortOpinion::where('uuid',$id)->with('user')->first();
        return view('frontend.opinions.show.share',compact('opinion'));
    }



    public function store(Request $request)
    {
        $type=$request->input('type');
        if($type=='IMAGE'){
            $files = $request->file('files');
            $finalimages=explode(',',$request->input('cover'));
            $imgs=array();
            foreach($files as $file){
                $filenameWithExt=$file->getClientOriginalName();
                if(in_array($filenameWithExt,$finalimages)){
                    $uniqueid=uniqid();
                    $original_name=$file->getClientOriginalName();
                    $original_size=$file->getSize();
                    $extension=$file->getClientOriginalExtension();
                    $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;

                    //Saving Original Size Image
                    $thumb_opinion= Image::make($file->getRealPath())->encode($extension);
                     Storage::disk('s3')->put('storage/app/public/opinion/'.$filename, (string)$thumb_opinion,'public');

                    // Resize image in 314x240
                     $imagename = Carbon::now()->format('Ymd').'_'.$uniqueid.'_314x240'.'.'.$extension; 
                    $thumb_opinion= Image::make($file->getRealPath())->resize(314, 240)->encode($extension);
                    //Need to change this to cloudfront URL #01:
                    //  $thumb_opinion->save('../storage/app/public/opinion/'.'/'.$imagename);

                     //Store Here
                     Storage::disk('s3')->put('storage/app/public/opinion/'.$imagename, (string)$thumb_opinion,'public');
                     // End Of Resize image in 314x240

                     // Resize image in 500x320
                     $imagename = Carbon::now()->format('Ymd').'_'.$uniqueid.'_500x320'.'.'.$extension; 
                     $thumb_opinion= Image::make($file->getRealPath())->resize(500, 320)->encode($extension);
                    //  $thumb_opinion = Image::make($file->getRealPath())->resize(500,  null, function ($constraint) {
                    //           $constraint->aspectRatio();
                    //       });
                    //  $thumb_opinion->save('../storage/app/public/opinion/'.'/'.$imagename);
                     // End Of Resize image in 500x320
                     Storage::disk('s3')->put('storage/app/public/opinion/'.$imagename, (string)$thumb_opinion,'public');

                    // $imagepath=url('/storage/opinion/'.$filename);
                    $imagepath = 'https://d20g1jo8qvj2jf.cloudfront.net/storage/app/public/opinion/'.$filename;
                    $path=$file->storeAs('public/opinion',$filename);
                    $size=$this->optimize_image($extension,'opinion',$filename,$original_size);
                    $this->save_file_to_db($uniqueid,$imagepath,$filename,$original_name,'OPINION_COVER_IMAGE',$size,$extension,Auth::user()->id);
                    array_push($imgs,$imagepath);
                }
            }
            $cover=implode(",",$imgs);
        }elseif($type=='YOUTUBE'){
            $cover=$request->input('cover');
        }elseif($type=='GIF'){
            $cover=$request->input('cover');
        }elseif($type=='EMBED'){
            $cover=$request->input('cover');
        }else{
            $cover=NULL;
        }
        $body_temp = $request->input('body');
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
          $body=$request->input('body');
        }
        $plain_body=$body;
        $cpanel_body=$body;
        
        $hash_pattern="/#(\w+)/";
        preg_match_all($hash_pattern, $body, $hashtags);
        $opinionThreads=[];

        if($request->has('thread')){
        $defaultThread=Thread::find($request->input('thread'));
        array_push($opinionThreads,$request->input('thread'));
        }

        if(count($hashtags[1])>0){
            $hash_tags_to_store=implode(',',array_map(function ($str) { return "#$str"; },$hashtags[1]));

            foreach ($hashtags[1] as $hashtag) {
                $threadFound=Thread::whereRaw('LOWER(`name`) = ?',[trim(strtolower($hashtag))])->first();
                    if($threadFound){
                        $ThreadId=$threadFound->id;
                    }else{
                        $threadCreate=Thread::create(['name'=>$hashtag,'slug'=>str::slug(trim($hashtag),'-')]);
                        $ThreadId=$threadCreate->id;
                    }
                array_push($opinionThreads,$ThreadId);
                $body=str_replace('#'.$hashtag,'<a href="https://weopined.com/thread/'.$hashtag.'" data-id="'.$ThreadId.'" class="thread_link">#'.$hashtag.'</a>',$body);
                $cpanel_body=str_replace('#'.$hashtag,'<a href="/cpanel/thread/view/'.$ThreadId.'" class="thread_link">#'.$hashtag.'</a>',$cpanel_body);
            }

            if($request->has('thread')){
                $hashtags[1]=array_map('strtolower', $hashtags[1]);
                if(!in_array(strtolower($defaultThread->name),$hashtags[1])){
                    $body=$body.' <a href="https://weopined.com/thread/'.$defaultThread->name.'" data-id="'.$defaultThread->id.'" class="thread_link">#'.$defaultThread->name.'</a>';
                    $cpanel_body=$cpanel_body.' <a href="/cpanel/thread/view/'.$defaultThread->id.'" class="thread_link">#'.$defaultThread->name.'</a>';
                }
            }

        }else{
            if($request->has('thread')){
            $hashtag=$defaultThread->name;
            $body=$body.' <a href="https://weopined.com/thread/'.$hashtag.'"  data-id="'.$defaultThread->id.'" class="thread_link">#'.$hashtag.'</a>';
            $cpanel_body=$cpanel_body.' <a href="/cpanel/thread/view/'.$defaultThread->id.'" class="thread_link">#'.$defaultThread->name.'</a>';
            $hash_tags_to_store='#'.$hashtag;
            }else{
                return redirect()->back()->withInput();
            }
        }

        $pattern  = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#';
        preg_match_all($pattern,$request->input('body'), $matches);
        $all_urls = $matches[0];
        if(count($all_urls)>0){
            $infolinks=array();
            foreach($all_urls as $url){
                $body=str_replace($url,'',$body);
                $info=$this->fetch_data_from_url($url);
                array_push($infolinks,$info);
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
        $opinion->uuid=uniqid();
        $opinion->body=$body;
        $opinion->plain_body=$plain_body;
        $opinion->cpanel_body=$cpanel_body;
        $opinion->hash_tags=$hash_tags_to_store;

        $opinion->cover=$cover;
        $opinion->cover_type=$type;
        $opinion->links=$links;
        $opinion->user_id=auth()->user()->id;
        $opinion->save();

        if(count($opinionThreads)>0){
            $opinion->threads()->sync(array_unique($opinionThreads));
        }
        $this->notify_followers($opinion,'ShortOpinionCreated');
        return redirect()->back();
    }


    // function for delete opinion
    public function delete_opinion(Request $request)
    {
           $short_opinion=ShortOpinion::where('id',$request->input('deleteid'))->first();
           if($short_opinion && $short_opinion->user_id==auth()->user()->id)
           {
                DB::transaction(function () use($short_opinion){
                    DB::table('short_opinion_likes')->where('short_opinion_id', '=', $short_opinion->id)->update(['is_active' => 0]);
                    DB::table('shares')->where('short_opinion_id', '=', $short_opinion->id)->update(['is_active' => 0]);
                    DB::table('short_opinion_comments')->where('short_opinion_id', '=', $short_opinion->id)->update(['is_active' => 0]);
                    DB::table('thread_opinions')->where('short_opinion_id', '=', $short_opinion->id)->update(['is_active' => 0]);
                    DB::table('shares')->where('short_opinion_id', '=', $short_opinion->id)->update(['is_active' => 0]);
                    $opinion_comments=DB::table('short_opinion_comments')->where('short_opinion_id',$short_opinion->id)->get();
                    foreach($opinion_comments as $op_comment){
                        DB::table('short_opinion_comments_likes')->where('comment_id',$op_comment->id)->update(['is_active'=>0]);
                    }
                    DB::table('notifications')
                    ->where('data','like','%"event":"OPINION_LIKED"%')
                    ->where('data','like','%"opinion_id":'.$short_opinion->id.'%')
                    ->delete();
                    DB::table('short_opinions')->where('id', '=', $short_opinion->id)->update(['is_active' => 0,'updated_at'=>Carbon::now()]);
                });
                    if($request->ajax()){
                        return response()->json(array('status'=>'success','message'=>'Opinion Successfully Deleted'));
                    }else{
                        return redirect('/');
                    }
           }
           else{
                if($request->ajax()){
                    return response()->json(array('status'=>'error','message'=>'Unauthorized User To Delete Opinion'));
                }else{
                    return redirect('/');
                }
           }
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
        $followed_threads=ThreadFollower::where(['user_id'=>Auth::user()->id,'is_active'=>1])->with('thread')->orderBy('created_at','desc')->take(6)->get();
        foreach ($followed_threads as $followed_thread) {
             $followed_thread->thread->opinions_count = $followed_thread->thread->opinions_count + $followed_thread->thread->comment_count;
             //var_dump($followed_thread->opinions_count);
        }
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

         $query = ShortOpinion::query();
            $query->with(['threads','user:id,name,username,unique_id,image']);
            $query->withCount(['likes','comments']);
            $query->whereHas('threads', function ($q) use ($followed_threadids) {
                $q->whereIn('threads.id',$followed_threadids)->where(['short_opinions.is_active'=>1]);
            })->orWhere(function($subquey) use ($following_userids){
                $subquey->whereIn('user_id',$following_userids)->where(['short_opinions.is_active'=>1]);
            })->orWhere(function($subqueys) use ($liked_ids){
                $subqueys->whereIn('id',$liked_ids)->where(['short_opinions.is_active'=>1]);
            })->orWhere(function($subques) use ($commented_ids){
                $subques->whereIn('id',$commented_ids)->where(['short_opinions.is_active'=>1]);
            });
            $query->where(['is_active'=>1]);
            $query->orderBy('last_updated_at','desc');
            $opinions= $query->paginate(12);

            foreach($opinions as $op){
                event(new OpinionViewCounterEvent($op,$request->ip()));
            }
           



        $liked=$this->get_user_liked_opinionids();
        $disliked=$this->get_user_disliked_opinionids();
        //$liked_threads=$this->get_user_liked_threadids();
        $followed_threadids=$this->get_user_followed_threadids();
        if($request->ajax()){
          $view = (String) view('frontend.opinions.components.opinions_loop',compact('opinions','liked','disliked'));
          return response()->json(['html'=>$view]);
        }else{
            return view('frontend.opinions.show.feed',compact('followed_threads','opinions','liked','disliked','followed_threadids','followingids','influencers'));
        }
    }

    //Function to get likes view for opinions

    public function get_opinion_likes(Request $request){
       // var_dump($request->opinion_id);
        $opinion=ShortOpinion::where('id',$request->opinion_id)->where(['is_active'=>1])->first();
        
        $opinion_likes=ShortOpinionLike::where('short_opinion_id',$opinion->id)
        ->where('is_active',1)
        ->with('user')
        ->orderBy('liked_at','desc')
        ->paginate(20);
        //var_dump($opinion_likes->total());
        if($opinion_likes->total()>0){
            $followingids=Auth::guest()?[]:auth()->user()->active_followings->pluck('id')->toArray();
                $output='';
                $output_li='';
                for($i=0;$i<count($opinion_likes);$i++){
                    if(!empty($opinion_likes[$i]->user["username"]) && !empty($opinion_likes[$i]->user["unique_id"])){
                        $start='<li class="list-group-item">'.
                        '<div class="media align-items-center mb-2">'.
                                '<a class="mr-3" href="/@'.$opinion_likes[$i]->user["username"].'"><img class="rounded-circle" src="'.$opinion_likes[$i]->user["image"].'" height="50" width="50" alt="Go to the profile of '.$opinion_likes[$i]->user["name"].'"  onerror="this.onerror=null;this.src="/img/avatar.png";"></a>'.
                                '<div class="media-body">'.
                                    '<div class="d-flex justify-content-between align-items-center w-100">'.
                                            '<a  href="/@'.$opinion_likes[$i]->user["username"].'" style="color:#212121;">'.ucfirst($opinion_likes[$i]->user["name"]).'</a>';
                                            if($opinion_likes[$i]->user["id"]!=Auth::user()->id){
                                                if(!in_array($opinion_likes[$i]->user["id"],$followingids)){
                                                    $middle='<button data-userid="'.$opinion_likes[$i]->user["id"].'" class="followbtn followbtn_'.$opinion_likes[$i]->user["id"].' btn btn-sm btn-outline-success" style="display:block">Add To Circle <span><i class="fas fa-user-plus ml-2"></i><span></button>'.
                                                    '<button  data-userid="'.$opinion_likes[$i]->user["id"].'" class="followingbtn followingbtn_'.$opinion_likes[$i]->user["id"].' btn btn-sm btn-success" style="display:none">In Your Circle <span><i class="fas fa-check ml-2"></i><span></button>';
                                                }else{
                                                    $middle='<button  data-userid="'.$opinion_likes[$i]->user["id"].'" class="followbtn followbtn_'.$opinion_likes[$i]->user["id"].' btn btn-sm btn-outline-success" style="display:none">Add To Circle <span><i class="fas fa-user-plus ml-2"></i><span></button>'.
                                                    '<button  data-userid="'.$opinion_likes[$i]->user["id"].'" class="followingbtn followingbtn_'.$opinion_likes[$i]->user["id"].' btn btn-sm btn-success" style="display:block">In Your Circle <span><i class="fas fa-check ml-2"></i><span></button>';
                                                }
                                        }else{
                                            
                                            $middle='';
                                        }
                                        

                            $end='</div>'.
                                '</div>'   .
                        '</div>'.
                        '</li>';

                        $li=$start.$middle.$end;
                        $output_li=$output_li.$li;
                    }
                }

                $ul='<ul class="list-group list-group-flush">'.$output_li.'</ul>';
                if($opinion_likes->nextPageUrl()==null){
                    $button='';
                    $output=$output_li.$button;
                }else{
                    $nextpage=$opinion_likes->currentPage()+1;
                    $button=' <button class="btn btn-sm btn-primary btn-block loadmore_likes" data-nextpage="'. $nextpage.'">Load More</button>';
                    $output=$output_li.$button;
                }

            echo $output;
        }else{
            $output='<ul class="list-group list-group-flush"><li class="list-group-item"><p>No one liked this opinion yet.</p></li></ul>';
            echo $output;
        }
    }

    // Function to update the share count of opinions and articles

    public function updateShareCount(Request $request)
    {
        $opinion_id=$request->input('opinion_id');
        $post_id=$request->input('post_id');
        $user_id=$request->input('user_id');
        $plateform=$request->input('plateform');
        $ip_address=isset($_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:$request->ip();
        $share = new Shares();
        $share->short_opinion_id=$opinion_id;
        $share->post_id=$post_id;
        $share->user_id=$user_id;
        $share->plateform=$plateform;
        $share->ip_address=$ip_address;
        $share->shared_at=Carbon::now();
        $share->save();
        //var_dump($opinion_id,$user_id);
    }
    // function for adding and removing likes by userid , opinion_id
    // public function like_opinion(Request $request)
    // {
    //     $opinion_id=$request->input('opinion_id');
    //     $Agree_Disagree=$request->input('Agree_Disagree');
    //     $short_opinion=ShortOpinion::where(['id'=>$opinion_id,'is_active'=>1])->with('user')->first();
    //     $Liked=DB::table('short_opinion_likes')->where(['user_id'=>Auth::user()->id,'short_opinion_id'=>$opinion_id])->first();
    //     if($Liked){
    //         if($Agree_Disagree==$Liked->Agree_Disagree){
    //             //Removing Agree-Disagree
    //             Auth::user()->likes()->detach($opinion_id);
    //             // DB::table('notifications')
    //             // ->where('data','like','%"event":"OPINION_LIKED"%')
    //             // ->where('data','like','%"opinion_id":'.$opinion_id.'%')
    //             // ->where('data','like','%"sender_id":'.Auth::user()->id.'%')
    //             // ->delete();
    //             if($request->ajax()){
    //             $response=array('status'=>'like');
    //             return response()->json($response);
    //             }
    //         }else{
    //             Auth::user()->likes()->detach($opinion_id);
    //             Auth::user()->likes()->attach($opinion_id);
    //             DB::table('short_opinions')->where(['id'=>$short_opinion->id])->update(['last_updated_at'=>Carbon::now()]);
    //             DB::table('short_opinion_likes')->where(['short_opinion_id'=>$opinion_id, 'user_id'=>Auth::user()->id])->update(['Agree_Disagree'=>$Agree_Disagree]);
    //             $this->notify_followers($short_opinion,'ShortOpinionLiked');
    //             if($request->ajax()){
    //                 $response=array('status'=>'liked','Agree_Disagree'=>$Agree_Disagree);
    //                 return response()->json($response);
    //             }
    //         }
    //     }else{
    //         Auth::user()->likes()->attach($opinion_id);
    //         DB::table('short_opinions')->where(['id'=>$short_opinion->id])->update(['last_updated_at'=>Carbon::now()]);
    //         DB::table('short_opinion_likes')->where(['short_opinion_id'=>$opinion_id, 'user_id'=>Auth::user()->id])->update(['Agree_Disagree'=>$Agree_Disagree]);
    //         $this->notify_followers($short_opinion,'ShortOpinionLiked');
    //         if($request->ajax()){
    //             $response=array('status'=>'liked','Agree_Disagree'=>$Agree_Disagree);
    //             return response()->json($response);
    //          }
    //     }
    // }


    
    //function for Agree/Disagree
    public function Agree_disagree_opinion(Request $request){
        $result_AD='';
        $opinion_id=$request->input('opinion_id');
	    $Agree_Disagree=$request->input('Agree_Disagree');
		$result_AD=$Agree_Disagree;

        if($Agree_Disagree == "0" || $Agree_Disagree=="1"){
					 
        }
        else{
              $response=array('status'=>'error','result'=>0,'message'=>'Agree Disagree Values are incorrect' );
               return response()->json($response,200);
        }
								 
        $opinion=ShortOpinion::where(['id'=>$opinion_id])->with('user')->first();
         //*******************************************************************************
         //*******************************************************************************
            $Liked=ShortOpinionLike::where(['user_id'=>Auth::user()->user_id,'short_opinion_id'=>$opinion_id])->first();
            if($Liked){
                if($Agree_Disagree == $Liked ->Agree_Disagree) //If prev agreedisagree value and currrent val is same then delete the record
							{			
								DB::table('short_opinion_likes')
								->where('id','=',$Liked-> id)
								 ->delete();
								$result_AD='';
							// //detach record
							 ($Agree_Disagree =='0') ? $result='disagreedel' :$result=' agreedel';	
                            }else //update record
							{
								$result='';
								//$result= 'id=>'.$Liked->id . 'Agree_Disagree=>'. $Agree_Disagree;
								ShortOpinionLike::where(['id'=>$Liked->id])->update(['Agree_Disagree'=> $Agree_Disagree,'liked_at'=>Carbon::now()]);
								
							}
            }else{
                $ShortOpinionLike = new ShortOpinionLike();
							$ShortOpinionLike ->user_id= Auth::user()->user_id;
							$ShortOpinionLike ->short_opinion_id= $opinion_id;
							$ShortOpinionLike ->Agree_Disagree= $Agree_Disagree;
							$ShortOpinionLike->save();
							($Agree_Disagree =='0')?  	$result='disagree' :$result='agree';
            }


             //*******************************************************************************
            if($opinion){
                $opinion_liked=ShortOpinionLike::where(['user_id'=>Auth::user()->user_id,'short_opinion_id'=>$opinion_id])->first();
                if($opinion_liked){
                    //Auth::user()->user->likes()->detach($opinion_id);
                    DB::table('notifications')
                    ->where('data','like','%"event":"OPINION_LIKED"%')
                    ->where('data','like','%"opinion_id":'.$opinion_id.'%')
                    ->where('data','like','%"sender_id":'.Auth::user()->user_id.'%')
                    ->delete();
                    $count=DB::table('short_opinion_likes')->where(['short_opinion_id'=>$opinion_id])->count();
                    $response=array('status'=>'success','result'=>0,'message'=>'Opinion action Updated','total'=>$count,'Agree_Disagree'=> $result_AD);

                    
                    return response()->json($response,200);
                }else{
                   // Auth::user()->user->likes()->attach($opinion_id);
                    $this->notify_followers($opinion,'ShortOpinionLiked');
                    ShortOpinion::where(['id'=>$opinion->id])->update(['last_updated_at'=>Carbon::now()]);
                    $count=DB::table('short_opinion_likes')->where(['short_opinion_id'=>$opinion_id])->count();
                    $response=array('status'=>'success','result'=>1,'message'=>'Opinion action updated','total'=>$count,'Agree_Disagree'=> $result_AD);
                    return response()->json($response,200);
                }
            }else{
                $response=array('status'=>'error','result'=>0,'errors'=>'Opinion not found');
                return response()->json($response, 200);
            }
        
    }

    // function for like thread
    public function like_thread(Request $request){
        $thread=Thread::where('id',$request->input('id'))->first();
        $thread_id=$thread->id;
        if($thread){
            $Liked=ThreadLike::where(['user_id'=>Auth::user()->id,'thread_id'=>$thread_id])->exists();
            if($Liked){
                Auth::user()->liked_thread()->detach($thread_id);
                DB::table('notifications')
                ->where('data','like','%"event":"THREAD_LIKED"%')
                ->where('data','like','%"thread_id":'.$thread_id.'%')
                ->where('data','like','%"sender_id":'.Auth::user()->id.'%')
                ->delete();
                $status='like';
            }else{
                Auth::user()->liked_thread()->attach($thread_id);
                $this->notify_followers($thread,'ThreadLiked');
                $status='liked';
            }

            if($request->ajax()){
                return response()->json(array('status'=>$status));
            }else{
                return redirect()->back();
            }
        }else{
            if($request->ajax()){
                return response()->json(array('status'=>'error','message'=>'Thread Not Found'));
            }else{
                return redirect()->back();
            }
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
