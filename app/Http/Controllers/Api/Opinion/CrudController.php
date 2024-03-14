<?php

namespace App\Http\Controllers\Api\Opinion;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionLike;
use App\Model\Thread;
use App\Model\Achievement;
use App\Model\ThreadOpinion;
use App\Model\User;
use App\Model\UserDevice;
use App\Model\Point;
use DB;
use Carbon\Carbon;
use App\Http\Helpers\VideoStream;
use App\Jobs\AndroidPush\ShortOpinionCreatedJob;
use Notification;
use App\Notifications\Frontend\ShortOpinionCreated;
use App\Events\OpinionViewCounterEvent;

use Illuminate\Contracts\Bus\Dispatcher;
use App\Jobs\Opinion\TranscodeVideoJob;
use App\Jobs\Resize\ResizeImageJob;
use Artisan;
use App\Model\GamificationReward;
use App\Jobs\AndroidPush\AchievementUnlockedJob;


class CrudController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api',['except'=>['read','embed','stream_video']]);
    }

    public function read(Request $request,$id){
        try{
            if(!isset($id)) {
                $response=array('status'=>'error','result'=>0,'errors'=>'opinion_id is required');
                return response()->json($response, 200);
           }else{
                 $user_id=-1;
                 $my_liked_opinionids=[];
                 $opinion=ShortOpinion::where(['id'=>$id,'is_active'=>1])->with('user:id,name,username,unique_id,image')->first();

                 if($request->header('Authorization')){
                    $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                    $my_liked_opinionids=$this->my_liked_opinionids($user_id);
                 }

                 if($opinion){
                    event(new OpinionViewCounterEvent($opinion,$request->ip()));
                    $formatted_opinion=$this->formatted_opinion_new($opinion,$my_liked_opinionids);
                    $response=array('status'=>'success','result'=>1,'opinion'=>$formatted_opinion);
                    return response()->json($response, 200);
                 }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'opinion not found');
                    return response()->json($response, 200);
                 }
           }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }
    public function read_test(Request $request,$id){
        try{
            if(!isset($id)) {
                $response=array('status'=>'error','result'=>0,'errors'=>'opinion_id is required');
                return response()->json($response, 200);
           }else{
                 $user_id=-1;
                 $my_liked_opinionids=[];
                 $opinion=ShortOpinion::where(['id'=>$id,'is_active'=>1])->with('user:id,name,username,unique_id,image')->first();
                

                 if($request->header('Authorization')){
                    $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                    $my_liked_opinionids=$this->my_liked_opinionids($user_id);
                 }
                 $my_agreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>1,'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
			    $my_disagreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>0,'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
			    $Agreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>1])->pluck('short_opinion_id')->toArray();
			    $Disagreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>0])->pluck('short_opinion_id')->toArray();

                 if($opinion){
                    event(new OpinionViewCounterEvent($opinion,$request->ip()));
                    $formatted_opinion=$this->formatted_opinion_AD($opinion,$my_liked_opinionids,$Agreeids,$Disagreeids,$my_agreed_opinionids,$my_disagreed_opinionids);//formatted_opinion($found,$my_liked_opinionids);//,$my_agreed_opinionids,$my_disagreed_opinionids);//formatted_opinion($found,$my_liked_opinionids);

                    $response=array('status'=>'success','result'=>1,'opinion'=>$formatted_opinion);
                    return response()->json($response, 200);
                 }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'opinion not found');
                    return response()->json($response, 200);
                 }
           }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }
    public function read_by_uuid(Request $request,$id){
        try{
            if(!isset($id)) {
                $response=array('status'=>'error','result'=>0,'errors'=>'opinion_id is required');
                return response()->json($response, 200);
           }else{
                 $user_id=-1;
                 $my_liked_opinionids=[];
                 $opinion=ShortOpinion::where(['uuid'=>$id,'is_active'=>1])->with('user:id,name,username,unique_id,image')->first();

                 if($request->header('Authorization')){
                    $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                    $my_liked_opinionids=$this->my_liked_opinionids($user_id);
                 }
                 $my_agreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>1,'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
                 $my_disagreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>0,'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
                 $Agreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>1])->pluck('short_opinion_id')->toArray();
                 $Disagreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>0])->pluck('short_opinion_id')->toArray();

                 if($opinion){
                    event(new OpinionViewCounterEvent($opinion,$request->ip()));
                    $formatted_opinion=$this->formatted_opinion_AD($opinion,$my_liked_opinionids,$Agreeids,$Disagreeids,$my_agreed_opinionids,$my_disagreed_opinionids);//formatted_opinion($found,$my_liked_opinionids);//,$my_agreed_opinionids,$my_disagreed_opinionids);//formatted_opinion($found,$my_liked_opinionids);
                    $response=array('status'=>'success','result'=>1,'opinion'=>$formatted_opinion);
                    return response()->json($response, 200);
                 }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'opinion not found');
                    return response()->json($response, 200);
                 }
           }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function edit(Request $request,$id){
        try{
            if(!isset($id)) {
                $response=array('status'=>'error','result'=>0,'errors'=>'opinion_id is required');
                return response()->json($response, 200);
           }else{
                 $user_id=Auth::user()->user_id;
                 $my_liked_opinionids=$this->my_liked_opinionids($user_id);
                 $opinion=ShortOpinion::where(['id'=>$id,'is_active'=>1,'user_id'=>Auth::user()->user_id])->with('threads:thread_id,name','user:id,name,username,unique_id,image')->first();
                 if($opinion){
                    $formatted_opinion=$this->formatted_opinion_new($opinion,$my_liked_opinionids);
                    $response=array('status'=>'success','result'=>1,'opinion'=>$formatted_opinion);
                    return response()->json($response, 200);
                 }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'opinion not found');
                    return response()->json($response, 200);
                 }
           }
         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function likes(Request $request,$id){
        try{
            if(!isset($id)) {
                $response=array('status'=>'error','result'=>0,'errors'=>'opinion_id is required');
                return response()->json($response, 200);
            }else{
                $opinion=ShortOpinion::where(['id'=>$id,'is_active'=>1])->with('user:id,name,username,unique_id,image')->first();
                if($opinion){
                    $opinion_likes=ShortOpinionLike::where(['short_opinion_id'=>$opinion->id,'is_active'=>1])->with('user:id,name,username,unique_id,image')->has('user')->paginate(20);
                    $formatted_likes=$opinion_likes->getCollection()->transform(function($like,$key){
                        $this->remove_null($like);
                        $is_followed=0;
                        if(Auth::check()){
                            $is_followed=in_array($like->user->id, Auth::user()->user->active_followings->pluck('id')->toArray())?1:0;
                        }else{
                            $is_followed=0;
                        }
                        $like->liked_at=Carbon::parse($like->liked_at)->format('j M Y , h:i A');
                        $like->user['is_followed']=$is_followed;
                        return $like;
                    });
                    $meta=$this->get_meta($opinion_likes);
                    $response=array('status'=>'success','result'=>1,'opinion_likes'=>$formatted_likes,'meta'=>$meta);
                    return response()->json($response, 200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'opinion not found');
                    return response()->json($response, 200);
                }
            }
         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function Agree_Disagrees(Request $request,$id){
        try{
            if(!isset($id)) {
                $response=array('status'=>'error','result'=>0,'errors'=>'opinion_id is required');
                return response()->json($response, 200);
            }else{
                $opinion=ShortOpinion::where(['id'=>$id,'is_active'=>1])->with('user:id,name,username,unique_id,image')->first();
                if($opinion){
                    $opinion_likes=ShortOpinionLike::where(['short_opinion_id'=>$opinion->id,'is_active'=>1])->with('user:id,name,username,unique_id,image')->has('user')->paginate(20);
                    $formatted_likes=$opinion_likes->getCollection()->transform(function($like,$key){
                        $this->remove_null($like);
                        $is_followed=0;
                        if(Auth::check()){
                            $is_followed=in_array($like->user->id, Auth::user()->user->active_followings->pluck('id')->toArray())?1:0;
                        }else{
                            $is_followed=0;
                        }
                        $like->liked_at=Carbon::parse($like->liked_at)->format('j M Y , h:i A');
                        $like->user['is_followed']=$is_followed;
                        return $like;
                    });
                    $meta=$this->get_meta($opinion_likes);
                    $response=array('status'=>'success','result'=>1,'opinion_AD'=>$formatted_likes,'meta'=>$meta);
                    return response()->json($response, 200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'opinion not found');
                    return response()->json($response, 200);
                }
            }
         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function embed(Request $request,$id){
        try{
            if(!isset($id)){
                $response=array('status'=>'error','result'=>0,'errors'=>'opinion_id is required');
                return response()->json($response, 200);
            }else{
                $opinion=ShortOpinion::where(['id'=>$id,'is_active'=>1])->with('user:id,name,username,unique_id,image')->first();
                if($opinion){
                    $url=url('@'.$opinion->user['username'].'/opinion/'.$opinion->uuid.'/share');
                    $embed_code='<iframe src="'.$url.'" height="700" width="100%" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media"  onload=javascript:(function(o){o.style.height=o.contentWindow.document.body.scrollHeight+'.'"px"'.';}(this)); />';
                    $response=array('status'=>'success','result'=>1,'embed_code'=>$embed_code);
                    return response()->json($response, 200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'opinion not found');
                    return response()->json($response, 200);
                }
            }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    //Updating points  of user for adding a opinion
    public function store(Request $request){
       try{
            $validator = Validator::make($request->all(),[
                'platform'=>'required',
                'type'=>'required',
                'body'=>'required',
                'title'=>'required'
            ]);
            if($validator->fails()){
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            }else{
                $opinion_uuid=uniqid();
                $type=$request->input('type');

                
                $community_id = $request->input('community_id');
                $news_id = $request->input('news_id');
                if($type=='IMAGE'){
                    $thumbnail=NULL;
                    if($request->hasFile('cover')){
                        if(is_array($request->file('cover')))
                        {
                            $imgs=array();
                            foreach($request->file('cover') as $file) {
                                $uniqueid=uniqid();
                                $original_name=$file->getClientOriginalName();
                                $size=$file->getSize();
                                $extension=$file->getClientOriginalExtension();
                                if($size>5048576){
                                    $response=array('status'=>'error','result'=>0,'errors'=>'Image is larger than 5 MB');
                                    return response()->json($response, 200);
                                }else if(!in_array(strtolower($extension),["jpg","jpeg","png","svg","gif"])){
                                    $response=array('status'=>'error','result'=>0,'errors'=>'Image format is invalid');
                                    return response()->json($response, 200);
                                }else{
                                    $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                                    $imagepath=url('/storage/opinion/'.$filename);
                                    $path=$file->storeAs('public/opinion',$filename);
                                    $size=$this->optimize_image($extension,'opinion',$filename,$size);
                                    $this->save_file_to_db($uniqueid,$imagepath,$filename,$original_name,'OPINION_COVER_IMAGE',$size,$extension,Auth::user()->user_id);
                                    try{
                                        $job = (new ResizeImageJob(storage_path('app/public/opinion/'.$filename),storage_path('app/public/opinion/'),[[314,240]]))->onQueue('default');
                                        app(Dispatcher::class)->dispatch($job);
                                    }catch(\Exception $e){}
                                    array_push($imgs,$imagepath);
                                }
                            }
                            $cover=implode(",",$imgs);
                        }
                        else{   // single file
                                $file = $request->file('cover');
                                $uniqueid=uniqid();
                                $original_name=$file->getClientOriginalName();
                                $size=$file->getSize();
                                $extension=$file->getClientOriginalExtension();
                                if($size>5048576){
                                    $response=array('status'=>'error','result'=>0,'errors'=>'Image is larger than 5 MB');
                                    return response()->json($response, 200);
                                }else if(!in_array(strtolower($extension),["jpg","jpeg","png","svg","gif"])){
                                    $response=array('status'=>'error','result'=>0,'errors'=>'Image format is invalid');
                                    return response()->json($response, 200);
                                }else{
                                    $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                                    $imagepath=url('/storage/opinion/'.$filename);
                                    $path=$file->storeAs('public/opinion',$filename);
                                    $size=$this->optimize_image($extension,'opinion',$filename,$size);
                                    $this->save_file_to_db($uniqueid,$imagepath,$filename,$original_name,'OPINION_COVER_IMAGE',$size,$extension,Auth::user()->user_id);
                                    $cover=$imagepath;
                                    try{
                                        $job = (new ResizeImageJob(storage_path('app/public/opinion/'.$filename),storage_path('app/public/opinion/'),[[314,240]]))->onQueue('default');
                                        app(Dispatcher::class)->dispatch($job);
                                    }catch(\Exception $e){}
                                }
                        }
                    }else{
                        $response=array('status'=>'error','result'=>0,'errors'=>'Image is required');
                        return response()->json($response, 200);
                    }
                }else if($type=='VIDEO'){
                    if($request->hasFile('cover')){
                        $file = $request->file('cover');
                        $uniqueid=uniqid();
                        $original_name=$file->getClientOriginalName();
                        $size=$file->getSize();
                        $extension=$file->getClientOriginalExtension();
                        /* if($size>10048576){
                            $response=array('status'=>'error','result'=>0,'errors'=>'Video is larger than 10 MB');
                            return response()->json($response, 200);
                        } */
                        $duration_exceeded=$this->check_video_duration_exceeded($file->getRealPath());
                        if($duration_exceeded){
                            $response=array('status'=>'error','result'=>0,'errors'=>'Video duration is exceeded , more than 3 minutes not allowed');
                            return response()->json($response, 200);
                        }
                        else if(!in_array(strtolower($extension),['mp4','webm']))
                        {
                            $response=array('status'=>'error','result'=>0,'errors'=>'Video format is invalid');
                            return response()->json($response, 200);
                        }else{
                            $filename_without_ext=Carbon::now()->format('Ymd').'_'.$uniqueid;
                            $filename=$filename_without_ext.'.'.$extension;
                            $videopath=url('/storage/videos/'.$filename);
                            $path=$file->storeAs('public/videos',$filename);
                            $this->save_file_to_db($uniqueid,$videopath,$filename,$original_name,'OPINION_COVER_VIDEO',$size,$extension,Auth::user()->user_id);
                            $cover=$videopath;
                            try{
                                $job = (new TranscodeVideoJob($path,$opinion_uuid,$uniqueid,Auth::user()->user_id))->onQueue('videos');
                                app(Dispatcher::class)->dispatch($job);
                                //Artisan::call('video:transcode',['opinion_uuid' => $opinion_uuid]);
                            }catch(\Exception $e){}
                        }
                    }else{
                        $response=array('status'=>'error','result'=>0,'errors'=>'Video file is required');
                        return response()->json($response, 200);
                    }

                    if($request->hasFile('thumbnail')){
                        $thumbnail_file = $request->file('thumbnail');
                        $thumbnail_original_name=$thumbnail_file->getClientOriginalName();
                        $thumbnail_size=$thumbnail_file->getSize();
                        $thumbnail_extension=$thumbnail_file->getClientOriginalExtension();
                        if($thumbnail_size>5024288){
                            $response=array('status'=>'error','result'=>0,'errors'=>'Video thumbnail is larger than 5 MB');
                            return response()->json($response, 200);
                        }else if(!in_array(strtolower($thumbnail_extension),['jpg','png','jpeg','gif']))
                        {
                            $response=array('status'=>'error','result'=>0,'errors'=>'Video thumbnail format is invalid');
                            return response()->json($response, 200);
                        }else{
                            $thumbnail_filename=$filename_without_ext.'_thumbnail.'.$thumbnail_extension;
                            $thumbnail_path=url('/storage/thumbnails/'.$thumbnail_filename);
                            $thumbnail_file->storeAs('public/thumbnails',$thumbnail_filename);
                            $this->save_file_to_db(uniqid(),$thumbnail_path,$thumbnail_filename,$thumbnail_original_name,'OPINION_COVER_VIDEO_THUMBNAIL',$thumbnail_size,$thumbnail_extension,Auth::user()->user_id);
                            $thumbnail=$thumbnail_path;
                        }
                    }else{
                        $response=array('status'=>'error','result'=>0,'errors'=>'Video thumbnail is required');
                        return response()->json($response, 200);
                    }

                }else if($type=='YOUTUBE' || $type=='GIF'){
                    $cover=$request->input('cover');
                    $thumbnail=NULL;
                }else{
                    $cover=NULL;
                    $thumbnail=NULL;
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
                $title = $request->input('title');
                

                $hash_pattern="/#(\w+)/";
                preg_match_all($hash_pattern, $body, $hashtags);
                $opinion_threads=[];

                if($request->has('thread')){
                    $default_thread=Thread::where(['id'=>$request->input('thread'),'is_active'=>1])->first();
                    if($default_thread){
                        array_push($opinion_threads,$request->input('thread'));
                    }
                }

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
                    if($request->has('thread')){
                        $hashtag=$default_thread->name;
                        $body=$body.' <a href="https://weopined.com/thread/'.$hashtag.'"  data-id="'.$default_thread->id.'" class="thread_link">#'.$hashtag.'</a>';
                        $cpanel_body=$cpanel_body.' <a href="https://weopined.com/cpanel/thread/view/'.$default_thread->id.'" class="thread_link">#'.$default_thread->name.'</a>';
                        $hash_tags_to_store='#'.$hashtag;
                    }else{
                        $response=array('status'=>'error','result'=>0,'errors'=>'Please enter valid thread by typing # of minimum 3 characters.');
                        return response()->json($response, 200);
                    }
                }

                // finding links in opinion body and get info of links
                $pattern  = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#';
                preg_match_all($pattern,$request->input('body'), $matches);
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
                $data['body']=$body;
                $data['title']=$title;
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
                
                $reward = new GamificationReward();
                $reward->user_id = $opinion->user_id;
                $reward->reward_type = 'opinion_posting_event';
                $reward->reward_amount = 50;
                $reward->save();

                //count number of opinions by this user
                $opinion_count=ShortOpinion::where(['user_id'=>Auth::user()->user_id])->count();
                $achievement_id = 0;
                if($opinion_count==1){
                    $achievement_id = 1;
                }else if($opinion_count==5){
                    $achievement_id = 2;
                }else if($opinion_count==10){
                    $achievement_id = 3;
                }else if($opinion_count==100){
                    $achievement_id = 7;
                }else if($opinion_count==500){
                    $achievement_id = 9;
                }else{
                    $achievement_id = 0;
                }

                if($achievement_id!=0){
                    $achievement = DB::table('achievements')->where('achievement_id', $achievement_id)->first();
                    $ruser_id = Auth::user()->user_id;
                    
                            $userHasUnlockedAchievement = DB::table('user_achievements')
                                ->where('user_id', $ruser_id)
                                ->where('achievements_id', $achievement_id)
                                ->exists();
                    
                            if (!$userHasUnlockedAchievement) {
                                DB::table('user_achievements')->insert([
                                    'user_id' => $ruser_id,
                                    'achievements_id' => $achievement_id,
                                ]);
                    
                                $reward2 = new GamificationReward();
                                $reward2->user_id = $ruser_id;
                                $reward2->reward_type = 'achievement: ' . $achievement->title;
                                $reward2->reward_amount = $achievement->reward;
                                $reward2->save();

                                $achievement2 = Achievement::where('achievement_id', $achievement_id)->first();
                        

                                $follower_ids =[$ruser_id,10362];
                                $fcm_tokens=UserDevice::whereIn('user_id',$follower_ids)->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
                    
                                foreach(array_chunk($fcm_tokens,100) as $chunk){
                                    dispatch(new AchievementUnlockedJob($achievement2,Auth::user()->user,$chunk));
                                }
                            }
                }


                if(count($opinion_threads)>0){
                    $opinion->threads()->sync(array_unique($opinion_threads));
                }
                $my_liked_opinionids=$this->my_liked_opinionids(Auth::user()->user_id);
                $formatted_opinion=$this->formatted_opinion_new($opinion,$my_liked_opinionids);
                $this->notify_followers($formatted_opinion);
                $response=array('status'=>'success','result'=>1,'opinion'=>$formatted_opinion);
                return response()->json($response,200);
            }
          }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function stream_video(Request $request,$video_name){
        try{
            if(!isset($video_name)){
                $response=array('status'=>'error','result'=>0,'errors'=>'video_name is required');
                return response()->json($response, 200);
            }else{
                $videosDir = base_path('storage/app/public/videos');
                if(Storage::exists('public/videos/'.$video_name)) {
                    $stream = new VideoStream($videosDir.'/'.$video_name);
                    $stream->start();
                }else{
                        $response=array('status'=>'error','result'=>0,'errors'=>'video not found');
                        return response()->json($response, 200);
                }
            }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function update(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'opinion_id'=>'required',
                'platform'=>'required',
                'type'=>'required',
                'body'=>'required',
            ]);
            if($validator->fails()){
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            }else{
                $opinion=ShortOpinion::where(['id'=>$request->input('opinion_id'),'is_active'=>1,'user_id'=>Auth::user()->user_id])->first();
                if($opinion){
                        $old_cover=$opinion->cover;
                        $old_covertype=$opinion->cover_type;
                        $old_thumbnail=$opinion->thumbnail;

                            $type=$request->input('type');
                            if($type=='IMAGE'){
                                $thumbnail=NULL;
                                if($request->hasFile('cover')){
                                    if(is_array($request->file('cover')))
                                    {
                                        $imgs=array();
                                        foreach($request->file('cover') as $file) {
                                            $uniqueid=uniqid();
                                            $original_name=$file->getClientOriginalName();
                                            $size=$file->getSize();
                                            $extension=$file->getClientOriginalExtension();
                                            if($size>5048576){
                                                $response=array('status'=>'error','result'=>0,'errors'=>'Image is larger than 5 MB');
                                                return response()->json($response, 200);
                                            }else if(!in_array(strtolower($extension),["jpg","jpeg","png","svg","gif"])){
                                                $response=array('status'=>'error','result'=>0,'errors'=>'Image format is invalid');
                                                return response()->json($response, 200);
                                            }else{
                                                $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                                                $imagepath=url('/storage/opinion/'.$filename);
                                                $path=$file->storeAs('public/opinion',$filename);
                                                $size=$this->optimize_image($extension,'opinion',$filename,$size);
                                                $this->save_file_to_db($uniqueid,$imagepath,$filename,$original_name,'OPINION_COVER_IMAGE',$size,$extension,Auth::user()->user_id);
                                                array_push($imgs,$imagepath);
                                            }
                                        }

                                        if($request->input('old_cover')!=null){
                                            $cover=$request->input('old_cover').",".implode(",",$imgs);
                                        }else{
                                            $cover=implode(",",$imgs);
                                        }

                                    }
                                    else{   // single file
                                            $file = $request->file('cover');
                                            $uniqueid=uniqid();
                                            $original_name=$file->getClientOriginalName();
                                            $size=$file->getSize();
                                            $extension=$file->getClientOriginalExtension();
                                            if($size>5048576){
                                                $response=array('status'=>'error','result'=>0,'errors'=>'Image is larger than 5 MB');
                                                return response()->json($response, 200);
                                            }else if(!in_array(strtolower($extension),["jpg","jpeg","png","svg","gif"])){
                                                $response=array('status'=>'error','result'=>0,'errors'=>'Image format is invalid');
                                                return response()->json($response, 200);
                                            }else{
                                                $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                                                $imagepath=url('/storage/opinion/'.$filename);
                                                $path=$file->storeAs('public/opinion',$filename);
                                                $size=$this->optimize_image($extension,'opinion',$filename,$size);
                                                $this->save_file_to_db($uniqueid,$imagepath,$filename,$original_name,'OPINION_COVER_IMAGE',$size,$extension,Auth::user()->user_id);

                                                if($request->input('old_cover')!=null){
                                                    $cover=$request->input('old_cover').",".$imagepath;
                                                }else{
                                                    $cover=$imagepath;
                                                }

                                            }
                                    }
                                }else{
                                    if ($request->input('old_cover')!=null){
                                        $cover=$request->input('old_cover');

                                    /*  $cover_images=explode(",",$request->input('old_cover'));
                                        $file_exists=false;
                                        foreach($cover_images as $ci){
                                            if(Storage::exists('public/opinion'.'/'.basename($ci))){
                                                $file_exists=true;
                                            }
                                        }
                                        if($file_exists){ */
                                        //  $cover=$request->input('old_cover');
                                        //}
                                    }else{
                                        $response=array('status'=>'error','result'=>0,'errors'=>'Image is required');
                                        return response()->json($response, 200);
                                    }
                                }
                            }else if($type=='VIDEO'){
                                if($request->hasFile('cover')){
                                    $file = $request->file('cover');
                                    $uniqueid=uniqid();
                                    $original_name=$file->getClientOriginalName();
                                    $size=$file->getSize();
                                    $extension=$file->getClientOriginalExtension();
                                    /* if($size>10048576){
                                        $response=array('status'=>'error','result'=>0,'errors'=>'Video is larger than 10 MB');
                                        return response()->json($response, 200);
                                    } */
                                    $duration_exceeded=$this->check_video_duration_exceeded($file->getRealPath());
                                    if($duration_exceeded){
                                        $response=array('status'=>'error','result'=>0,'errors'=>'Video duration is exceeded , more than 3 minutes not allowed');
                                        return response()->json($response, 200);
                                    }
                                    else if(!in_array(strtolower($extension),['mp4','webm']))
                                    {
                                        $response=array('status'=>'error','result'=>0,'errors'=>'Video format is invalid');
                                        return response()->json($response, 200);
                                    }else{
                                        $filename_without_ext=Carbon::now()->format('Ymd').'_'.$uniqueid;
                                        $filename=$filename_without_ext.'.'.$extension;
                                        $videopath=url('/storage/videos/'.$filename);
                                        $path=$file->storeAs('public/videos',$filename);
                                        $this->save_file_to_db($uniqueid,$videopath,$filename,$original_name,'OPINION_COVER_VIDEO',$size,$extension,Auth::user()->user_id);
                                        $cover=$videopath;
                                         try{
                                            $job = (new TranscodeVideoJob($path,$opinion->uuid,$uniqueid,Auth::user()->user_id))->onQueue('videos');
                                            app(Dispatcher::class)->dispatch($job);
                                        }catch(\Exception $e){}
                                    }
                                }else{
                                    if ($request->input('old_cover')!=null && Storage::exists('public/videos'.'/'.basename($request->input('old_cover')))){
                                        $cover=$request->input('old_cover');
                                    }else{
                                        $response=array('status'=>'error','result'=>0,'errors'=>'Video file is required');
                                        return response()->json($response, 200);
                                    }
                                }

                                if($request->hasFile('thumbnail')){
                                    $thumbnail_file = $request->file('thumbnail');
                                    $thumbnail_original_name=$thumbnail_file->getClientOriginalName();
                                    $thumbnail_size=$thumbnail_file->getSize();
                                    $thumbnail_extension=$thumbnail_file->getClientOriginalExtension();
                                    if($thumbnail_size>5024288){
                                        $response=array('status'=>'error','result'=>0,'errors'=>'Video thumbnail is larger than 5 MB');
                                        return response()->json($response, 200);
                                    }else if(!in_array(strtolower($thumbnail_extension),['jpg','png','jpeg','gif']))
                                    {
                                        $response=array('status'=>'error','result'=>0,'errors'=>'Video thumbnail format is invalid');
                                        return response()->json($response, 200);
                                    }else{
                                        $thumbnail_filename=$filename_without_ext.'_thumbnail.'.$thumbnail_extension;
                                        $thumbnail_path=url('/storage/thumbnails/'.$thumbnail_filename);
                                        $thumbnail_file->storeAs('public/thumbnails',$thumbnail_filename);
                                        $this->save_file_to_db(uniqid(),$thumbnail_path,$thumbnail_filename,$thumbnail_original_name,'OPINION_COVER_VIDEO_THUMBNAIL',$thumbnail_size,$thumbnail_extension,Auth::user()->user_id);
                                        $thumbnail=$thumbnail_path;
                                    }
                                }else{
                                    if ($old_thumbnail!=null && Storage::exists('public/thumbnails'.'/'.basename($old_thumbnail))){
                                        $thumbnail=$old_thumbnail;
                                    }else{
                                        $response=array('status'=>'error','result'=>0,'errors'=>'Video thumbnail is required');
                                        return response()->json($response, 200);
                                    }
                                }

                            }else if($type=='YOUTUBE' || $type=='GIF'){
                                $thumbnail=NULL;
                                $cover=$request->input('cover');
                            }else{
                                $thumbnail=NULL;
                                $cover= $old_cover;
                            }

                        $body=$request->input('body');
                        $plain_body=$request->input('body');
                        $cpanel_body=$request->input('body');

                        $hash_pattern="/#(\w+)/";
                        preg_match_all($hash_pattern, $body, $hashtags);
                        $opinion_threads=[];

                        if($request->has('thread')){
                            $default_thread=Thread::where(['id'=>$request->input('thread'),'is_active'=>1])->first();
                            if($default_thread){
                                array_push($opinion_threads,$request->input('thread'));
                            }
                        }

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
                                $body=str_replace('#'.$hashtag,'<a href="https://weopined.com/thread/thread/'.$hashtag.'" data-id="'.$thread_id.'" class="thread_link">#'.$hashtag.'</a>',$body);
                                $cpanel_body=str_replace('#'.$hashtag,'<a href="https://weopined.com/thread/cpanel/thread/view/'.$thread_id.'" class="thread_link">#'.$hashtag.'</a>',$cpanel_body);
                            }
                        }else{
                            if($request->has('thread')){
                                $hashtag=$default_thread->name;
                                $body=$body.' <a href="https://weopined.com/thread/thread/'.$hashtag.'"  data-id="'.$default_thread->id.'" class="thread_link">#'.$hashtag.'</a>';
                                $cpanel_body=$cpanel_body.' <a href="https://weopined.com/thread/cpanel/thread/view/'.$default_thread->id.'" class="thread_link">#'.$default_thread->name.'</a>';
                                $hash_tags_to_store='#'.$hashtag;
                            }else{
                                $response=array('status'=>'error','result'=>0,'errors'=>'Please enter valid thread by typing # of minimum 3 characters.');
                                return response()->json($response, 200);
                            }
                        }

                        // finding links in opinion body and get info of links
                        $pattern  = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#';
                        preg_match_all($pattern,$request->input('body'), $matches);
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
                            $links=json_encode($infolinks);
                        }else{
                            $links=NULL;
                        }

                        $data=[];
                        $data['body']=$body;
                        $data['plain_body']=$plain_body;
                        $data['cpanel_body']=$cpanel_body;
                        $data['hash_tags']=$hash_tags_to_store;
                        $data['cover']=$cover;
                        $data['type']=$type;
                        $data['links']=$links;
                        $data['thumbnail']=$thumbnail;
                        $updated_opinion=$this->save_opinion($opinion,$data,$opinion->uuid);
                        if(count($opinion_threads)>0){
                            $updated_opinion->threads()->sync(array_unique($opinion_threads));
                        }

                        $my_liked_opinionids=$this->my_liked_opinionids(Auth::user()->user_id);
                        $formatted_opinion=$this->formatted_opinion_new($updated_opinion,$my_liked_opinionids);
                        $response=array('status'=>'success','result'=>1,'opinion'=>$formatted_opinion);
                        return response()->json($response,200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'opinion not found');
                    return response()->json($response, 200);
                }
            }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    //Updating the points for destroying a opinion
    public function destroy(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'opinion_id'=>'required',
            ]);
            if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
                $short_opinion=ShortOpinion::where(['id'=>$request->input('opinion_id'),'is_active'=>1])->first();
                if($short_opinion && $short_opinion->user_id==auth()->user()->user_id)
                {
                    DB::transaction(function () use($short_opinion){
                        DB::table('short_opinion_likes')->where('short_opinion_id', '=', $short_opinion->id)->update(['is_active' => 0]);
                        DB::table('short_opinion_comments')->where('short_opinion_id', '=', $short_opinion->id)->update(['is_active' => 0]);
                        DB::table('thread_opinions')->where('short_opinion_id', '=', $short_opinion->id)->update(['is_active' => 0]);
                        $opinion_comments=DB::table('short_opinion_comments')->where('short_opinion_id',$short_opinion->id)->get();
                        foreach($opinion_comments as $op_comment){
                            DB::table('short_opinion_comments_likes')->where('comment_id',$op_comment->id)->update(['is_active'=>0]);
                            DB::table('short_opinion_comments_disagree')->where('comment_id',$op_comment->id)->update(['is_active'=>0]);
                        }
                        DB::table('notifications')
                        ->where('data','like','%"event":"OPINION_LIKED"%')
                        ->where('data','like','%"opinion_id":'.$short_opinion->id.'%')
                        ->delete();
                        DB::table('short_opinions')->where('id', '=', $short_opinion->id)->update(['is_active' => 0]);
                    });
                    $response=array('status'=>'success','result'=>1,'message'=>'Opinion deleted');
                    // $point=Point::where(['user_id'=>$short_opinion->user_id])->first();
                    // $point->post_points = $point->post_points-50;
                    // $point->save();
                    // if(Carbon::now()->diffInHours($point->update_at)==0) {
                    //     Point::where(['user_id'=>$short_opinion->user_id])->update([
                    //         'daily_points'=>$point->daily_points-50,
                    //     ]);
                    // }
                    //TODO Delete Rewards
                    return response()->json($response,200);
                }else{
                    $response=array('status'=>'error','result'=>0,'message'=>'Opinion not found');
                    return response()->json($response,200);
                }
           }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
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
        $opinion->user_id=auth()->user()->user_id;
        $opinion->platform=isset($data['platform'])?$data['platform']:'android';
        $opinion->community_id=isset($data['community_id'])?$data['community_id']:0;
        $opinion->news_id=isset($data['news_id'])?$data['news_id']:0;
        $opinion->save();
        return $opinion;
    }

    protected function notify_followers($opinion){
        $followers=auth()->user()->user->active_followers;
        $follower_ids=count($followers)>0?$followers->pluck('id')->toArray():[];
        $fcm_tokens=UserDevice::select('gcm_token')->distinct()->whereIn('user_id',$follower_ids)->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
        try{
            foreach(array_chunk($fcm_tokens,100) as $chunk){
                dispatch(new ShortOpinionCreatedJob($opinion,Auth::user()->user,$chunk));
            }
            foreach($followers as $follower){
                Notification::send($follower,new ShortOpinionCreated($opinion,Auth::user()->user,$fcm_tokens));
            }
        }catch(\Exception $e){}
    }

}
