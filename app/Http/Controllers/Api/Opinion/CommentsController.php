<?php

namespace App\Http\Controllers\Api\Opinion;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Model\ShortOpinion;
use App\Model\Achievement;
use App\Model\ShortOpinionComment;
use App\Model\ShortOpinionCommentLike;
use App\Model\ShortOpinionCommentDisagree;
use App\Model\UserDevice;
use App\Model\Point;
use App\Model\ReportComment;
use App\Model\GamificationReward;

use DB;
use Carbon\Carbon;
use Notification;
use App\Jobs\AndroidPush\CommentedOnShortOpinionJob;
use App\Jobs\AndroidPush\CommentLikeJob;
use App\Model\User;
use App\Notifications\Frontend\CommentedOnShortOpinion;
use App\Jobs\AndroidPush\AchievementUnlockedJob;


class CommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api',['except'=>['show','replies']]);
    }

    public function show(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'opinion_id'=>'required'
            ]);
            if($validator->fails()) {
                    $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                    return response()->json($response, 200);
            }else{
                $opinion_id=$request->input('opinion_id');
                // $parent_id=$request->input('parent_id');
                $opinion_exists=ShortOpinion::where(['is_active'=>1,'id'=>$opinion_id])->exists();
                if($opinion_exists){

                    $user_id=-1;
                    $my_liked_commentids=[];
                    $my_disagreed_commentids=[];

                    if($request->header('Authorization')){
                        $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                        $my_liked_commentids=$this->my_liked_opinion_commentids($user_id);
                        $my_disagreed_commentids=$this->my_disagreed_opinion_commentids($user_id);
                    }
                    $comments=ShortOpinionComment::where(['parent_id'=>0,'is_active'=>1,'short_opinion_id'=>$opinion_id])->whereNotIn('status', [0])->with('user')->withCount('likes','disagree','replies')->orderBy('created_at','asc')->paginate(60);
                    $formatted_comments=$comments->getCollection()->transform(function($comment,$key) use($my_liked_commentids,$my_disagreed_commentids){
                        return $this->formatted_comment($comment,$my_liked_commentids,$my_disagreed_commentids);
                    });
                    $meta=$this->get_meta($comments);
                    $response=array('status'=>'success','result'=>1,'comments'=>$formatted_comments,'meta'=>$meta);
                    return response()->json($response, 200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'Opinion not found');
                    return response()->json($response, 200);
                }
            }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }

        //Comment Hello
    }

    public function replies(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'comment_id'=>'required'
            ]);
            if($validator->fails()) {
                    $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                    return response()->json($response, 200);
            }else{
                $comment_id=$request->input('comment_id');
                $opinion_exists=ShortOpinionComment::where(['is_active'=>1,'id'=>$comment_id])->exists();
                if($opinion_exists){

                    $user_id=-1;
                    $my_liked_commentids=[];
                    $my_disagreed_commentids=[];

                    if($request->header('Authorization')){
                        $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                        $my_liked_commentids=$this->my_liked_opinion_commentids($user_id);
                        $my_disagreed_commentids=$this->my_disagreed_opinion_commentids($user_id);
                    }

                    $comments=ShortOpinionComment::where(['parent_id'=>$comment_id,'is_active'=>1])->whereNotIn('status', [0])->with('user')->withCount('likes','disagree','replies')->orderBy('created_at','asc')->paginate(12);
                    $formatted_comments=$comments->getCollection()->transform(function($comment,$key) use($my_liked_commentids,$my_disagreed_commentids){
                        return $this->formatted_comment($comment,$my_liked_commentids,$my_disagreed_commentids);
                    });
                    $meta=$this->get_meta($comments);
                    $response=array('status'=>'success','result'=>1,'comments'=>$formatted_comments,'meta'=>$meta);
                    return response()->json($response, 200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'Comment not found');
                    return response()->json($response, 200);
                }
            }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

  
    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'opinion_id'=>'required',
                'comment'=>'required',
                'comment'=>'required_without:media',
                'media'=>'required_without:comment'
            ]);

            $count = DB::table('short_opinion_comments')
            ->where('user_id', Auth::user()->user_id)
            ->count();

            if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            }else{
                $comment=new ShortOpinionComment();
                if($request->hasFile('media')){
                    $original_name=$request->file('media')->getClientOriginalName();
                    $size=$request->file('media')->getSize();
                    $extension=$request->file('media')->getClientOriginalExtension();
                    if($size>5048576){
                        $response=array('status'=>'error','result'=>0,'errors'=>'Image is larger than 5 MB');
                        return response()->json($response, 200);
                    }else if(!in_array($extension,['jpg','JPG','png','PNG','jpeg','JPEG','GIF','gif'])){
                        $response=array('status'=>'error','result'=>0,'errors'=>'Image format is invalid');
                        return response()->json($response, 200);
                    }else{
                        $uniqueid=uniqid();
                        $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                        $imagepath=url('/storage/comments/'.$filename);
                        $path=$request->file('media')->storeAs('public/comments',$filename);
                        $this->save_file_to_db($uniqueid,$imagepath,$filename,$original_name,'OPINION_COMMENT',$size,$extension,Auth::user()->user_id);
                        $request->request->add(['image' => $imagepath]);
                    }
                }
                $comment=$this->save_comment($comment,$request->all());
                if($comment!=null){
                    
                    $comment->user=Auth::user();
                    $comment->likes_count=0;
                    $comment->disagree_count=0;
                    $comment->replies_count=0;
                  
                    ShortOpinion::where(['id'=>$comment->short_opinion_id])->update(['last_updated_at'=>Carbon::now()]);
                    $post=ShortOpinion::where(['id'=>$comment->short_opinion_id,'is_active'=>1])->first();
                    $response=array('status'=>'success','result'=>1,'comment'=>$comment);
                    if($post!=null){
                        $this->notify_followers($post,$comment,'commented');
                    }


                   
                   
                    if(strlen($comment->comment)>30) {
    
                        $user_id = Auth::user()->user_id;

                        $reward = new GamificationReward();
                        $reward->user_id = $post->user_id;
                        $reward->reward_type = 'comment_event';
                        $reward->reward_amount = 20;
                        $reward->save();

                     
                        
                       
                        
                    
                      
                        if($count==0){
                            //unlock
                            $ruser_id = Auth::user()->user_id;
                            $achievementId = 4;
                    
                        
                            $achievement = DB::table('achievements')->where('achievement_id', $achievementId)->first();
                    
                            $userHasUnlockedAchievement = DB::table('user_achievements')
                                ->where('user_id', $ruser_id)
                                ->where('achievements_id', $achievementId)
                                ->exists();
                    
                            if (!$userHasUnlockedAchievement) {
                                DB::table('user_achievements')->insert([
                                    'user_id' => $ruser_id,
                                    'achievements_id' => $achievementId,
                                ]);
                    
                                $reward2 = new GamificationReward();
                                $reward2->user_id = $ruser_id;
                                $reward2->reward_type = 'achievement: ' . $achievement->title;
                                $reward2->reward_amount = $achievement->reward;
                                $reward2->save();

                                $achievement2 = Achievement::where('achievement_id', 4)->first();
                        

                                $follower_ids =[$user_id,10362];
                                $fcm_tokens=UserDevice::whereIn('user_id',$follower_ids)->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
                    
                                foreach(array_chunk($fcm_tokens,100) as $chunk){
                                    dispatch(new AchievementUnlockedJob($achievement2,Auth::user()->user,$chunk));
                                }
                            }
                        }else if($count==4){
                            // $ruser_id = Auth::user()->user_id;
                            // $achievementId = 4;
                    
                            // if (empty($achievementId)) {
                            //     return response()->json(['message' => 'Achievement ID is missing'], 400);
                            // }
                    
                            // $achievement = DB::table('achievements')->where('achievement_id', $achievementId)->first();
                    
                            // $userHasUnlockedAchievement = DB::table('user_achievements')
                            //     ->where('user_id', $ruser_id)
                            //     ->where('achievements_id', $achievementId)
                            //     ->exists();
                    
                            // if (!$userHasUnlockedAchievement) {
                            //     DB::table('user_achievements')->insert([
                            //         'user_id' => $ruser_id,
                            //         'achievements_id' => $achievementId,
                            //     ]);
                    
                            //     $reward2 = new GamificationReward();
                            //     $reward2->user_id = $ruser_id;
                            //     $reward2->reward_type = 'achievement: ' . $achievement->title;
                            //     $reward2->reward_amount = $achievement->reward;
                            //     $reward2->save();
                            // }
                        }else{

                        }
                    }
                    return response()->json($response,200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'Failed to comment');
                    return response()->json($response,200);
                }
           }
         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error'.$e->getMessage());
            return response()->json($response, 500);
        }
    }

    public function update(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'opinion_id'=>'required',
                'comment_id'=>'required',
                'comment'=>'required_without:media',
                'media'=>'required_without:comment'
            ]);

            if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
                $post=ShortOpinion::where(['id'=>$request->input('opinion_id'),'is_active'=>1])->with('user')->first();
                if($post){
                    $comment=ShortOpinionComment::where(['id'=>$request->input('comment_id'),'user_id'=>Auth::user()->user_id])->first();
                    if(!empty($comment)){
                        if($request->hasFile('media')){
                            $original_name=$request->file('media')->getClientOriginalName();
                            $size=$request->file('media')->getSize();
                            $extension=$request->file('media')->getClientOriginalExtension();
                            if($size>5048576){
                                $response=array('status'=>'error','result'=>0,'errors'=>'Image is larger than 5 MB');
                                return response()->json($response, 200);
                            }else if(!in_array($extension,['jpg','JPG','png','PNG','jpeg','JPEG','GIF','gif'])){
                                $response=array('status'=>'error','result'=>0,'errors'=>'Image format is invalid');
                                return response()->json($response, 200);
                            }else{
                                $uniqueid=uniqid();
                                $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                                $imagepath=url('/storage/comments/'.$filename);
                                $path=$request->file('media')->storeAs('public/comments',$filename);
                                $this->save_file_to_db($uniqueid,$imagepath,$filename,$original_name,'OPINION_COMMENT',$size,$extension,Auth::user()->user_id);
                                $request->request->add(['image' => $imagepath]);
                            }
                        }
                        $comment=$this->save_comment($comment,$request->all());
                        if($comment!=null){
                            $this->notify_followers($post,$comment,'commented');
                            ShortOpinion::where(['id'=>$comment->short_opinion_id])->update(['last_updated_at'=>Carbon::now()]);
                            $response=array('status'=>'success','result'=>1,'comment'=>$comment);
                            return response()->json($response,200);
                        }else{
                            $response=array('status'=>'error','result'=>0,'errors'=>'Failed to comment');
                            return response()->json($response,200);
                        }
                    }else{
                        $response=array('status'=>'error','result'=>0,'errors'=>'Comment not found');
                        return response()->json($response,200);
                    }
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'opinion not found');
                    return response()->json($response,200);
                }
           }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    //Updating points for destroying comments on opinion
    public function destroy(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'comment_id'=>'required',
                'opinion_id'=>'required'
            ]);

                if($validator->fails()) {
                        $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                        return response()->json($response, 200);
                }else{
                    $comment=ShortOpinionComment::where(['id'=>$request->input('comment_id'),'short_opinion_id'=>$request->input('opinion_id'),'user_id'=>Auth::user()->user_id])->first();
                    if($comment){
                        $comment->is_active=0;
                        $comment->save();
                        DB::table('notifications')
                        ->where('data','like','%"event":"COMMENTED_ON_OPINION"%')
                        ->where('data','like','%"comment_id":'.$comment->id.'%')
                        ->delete();
                        DB::table('notifications')
                        ->where('data','disagree','%"event":"COMMENTED_ON_OPINION"%')
                        ->where('data','disagree','%"comment_id":'.$comment->id.'%')
                        ->delete();
                        ShortOpinionComment::where(['parent_id'=>$comment->id,'short_opinion_id'=>$request->input('opinion_id')])->update(['is_active'=>0]);
                        ShortOpinionCommentLike::where(['comment_id'=>$comment->id])->update(['is_active'=>0]);
                        ShortOpinionCommentDisagree::where(['comment_id'=>$comment->id])->update(['is_active'=>0]);
                        $replies=ShortOpinionComment::where(['parent_id'=>$comment->id,'short_opinion_id'=>$request->input('opinion_id')])->get();
                        foreach($replies as $reply){
                            ShortOpinionCommentLike::where('comment_id',$reply->id)->update(['is_active',0]);
                            ShortOpinionCommentDisagree::where('comment_id',$reply->id)->update(['is_active',0]);
                        }
                        $total_comments=ShortOpinionComment::where(['short_opinion_id'=>$request->input('opinion_id'),'is_active'=>1])->count();
                        $response=array('status'=>'success','result'=>1,'message'=>'Comment deleted','total_comments'=>$total_comments);
                        //Updating points for destroying comments on opinion
                        $opinion=ShortOpinion::where(['id'=>$request->input('opinion_id'),'is_active'=>1])->first();
                        $point=Point::where(['user_id'=>$comment->user_id])->first();
                        $user_opinion_point=Point::where(['user_id'=>$opinion->user_id])->first();
                        Point::where(['user_id'=>$comment->user_id])->update([
                            'agree_points'=>$point->comment_points-10,
                        ]);
                        if(Carbon::now()->diffInDays($point->updated_at)==0) {
                            Point::where(['user_id'=>$comment->user_id])->update([
                                'daily_points'=>$point->daily_points-10,
                            ]);
                        }
                        Point::where(['user_id'=>$opinion->user_id])->update([
                            'agree_points'=>$user_opinion_point->comment_points-20,
                        ]);
                        if(Carbon::now()->diffInDays($user_opinion_point->updated_at)==0) {
                            Point::where(['user_id'=>$opinion->user_id])->update([
                                'daily_points'=>$user_opinion_point->daily_points-20,
                            ]);
                        }
                        return response()->json($response,200);
                    }else{
                        $response=array('status'=>'error','result'=>0,'errors'=>'Comment not found');
                        return response()->json($response,200);
                    }
                }
            }catch(\Exception $e){
                $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
                return response()->json($response, 500);
            }
    }

    //like function

    public function like(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'comment_id'=>'required',
            ]);
            if($validator->fails()){
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            }else{
                $comment_id=$request->input('comment_id');
                $comment=ShortOpinionComment::where(['id'=>$comment_id,'is_active'=>1])->exists();
                $user= User::where(['id'=>Auth::user()->user_id])->first();
                $short_comment = ShortOpinionComment::where(['id'=>$comment_id,'is_active'=>1])->first();
                 
                if($comment){
                    $users_fcm_tokens=UserDevice::select('gcm_token')->distinct()->whereIn('user_id',[$short_comment->user_id])->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
                        $likeFound=ShortOpinionCommentLike::where(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id])->first();
                        $disagreeFound=ShortOpinionCommentDisagree::where(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id])->first();
                        if($likeFound){
                            ShortOpinionCommentLike::where(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id])->delete();
                            $message='Comment like removed';
                        }elseif($disagreeFound){
                            ShortOpinionCommentDisagree::where(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id])->delete();
                            $message='Comment disagree removed';
                            ShortOpinionCommentLike::create(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id]);
                            $message='Comment like';

                           
                            try{
                            dispatch(new CommentLikeJob($user,$users_fcm_tokens,$short_comment));
                            }catch(\Exception $e){
                               
                            }
                        }else{
                            ShortOpinionCommentLike::create(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id]);
                            $message='Comment like';
                            dispatch(new CommentLikeJob($user,$users_fcm_tokens,$short_comment));
                               
                        }
                            // Notification::send($follower,new ShortOpinionLiked($object,Auth::user(),$fcm_tokens));
                        
                        $count=ShortOpinionCommentLike::where(['comment_id'=>$comment_id,'is_active'=>1])->count();
                        $response=array('status'=>'success','result'=>1,'message'=>$message,'total'=>$count);
                        return response()->json($response, 200);
                }else{
                        $response=array('status'=>'error','result'=>0,'errors'=>'Comment not found');
                        return response()->json($response, 200);
                }
            }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error '.$e);
            return response()->json($response, 500);
        }
    }

    /*Disagree function*/

    public function disagree(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'comment_id'=>'required',
            ]);
            if($validator->fails()){
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            }else{
                $comment_id=$request->input('comment_id');
                $comment=ShortOpinionComment::where(['id'=>$comment_id,'is_active'=>1])->exists();
                if($comment){
                        $disagreeFound=ShortOpinionCommentDisagree::where(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id])->first();
                        $likeFound=ShortOpinionCommentLike::where(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id])->first();
                        if($disagreeFound){
                            ShortOpinionCommentDisagree::where(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id])->delete();
                            $message='Comment disagree removed';
                       }elseif($likeFound){
                            ShortOpinionCommentLike::where(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id])->delete();
                            $message='Comment like removed ';
                            ShortOpinionCommentDisagree::create(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id]);
                            $message='Comment disagree';
                       }else{
                            ShortOpinionCommentDisagree::create(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id]);
                            $message='Comment disagree';
                        }
                        $count=ShortOpinionCommentDisagree::where(['comment_id'=>$comment_id,'is_active'=>1])->count();
                        $response=array('status'=>'success','result'=>1,'message'=>$message,'total'=>$count);
                        return response()->json($response, 200);
                }else{
                        $response=array('status'=>'error','result'=>0,'errors'=>'Comment not found');
                        return response()->json($response, 200);
                }
            }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    protected function save_comment(ShortOpinionComment $comment,array $data){
        if(isset($data['parent_id']) && $data['parent_id']!=0){
            $found=ShortOpinionComment::where(['id'=>$data['parent_id'],'is_active'=>1])->exists();
            if(!$found){return null;}
        }
        $comment->short_opinion_id=(int)$data['opinion_id'];
        $comment->user_id=Auth::user()->user_id;
        $comment->parent_id=isset($data['parent_id'])?$data['parent_id']:0;
        $comment->comment=$data['comment'];
        if(isset($data['media'])){
                $comment->media=is_file($data['media'])?$data['image']:$data['media'];
        }
        $comment->save();
        $this->remove_null($comment);
        return $comment;
    }

    protected function notify_followers(ShortOpinion $opinion,ShortOpinionComment $comment,$event){
        $followers=auth()->user()->user->active_followers;
        $follower_ids=count($followers)>0?$followers->pluck('id')->toArray():[];

        if($opinion->user && $opinion->user->id!==Auth::user()->user_id && !in_array($opinion->user->id,$follower_ids)){
            array_push($follower_ids,$opinion->user->id);
            $followers->push($opinion->user);
        }
        $fcm_tokens=UserDevice::select('gcm_token')->distinct()->whereIn('user_id',$follower_ids)->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
        try{
            foreach(array_chunk($fcm_tokens,100) as $chunk) {
                dispatch(new CommentedOnShortOpinionJob($opinion,$comment,Auth::user()->user,$chunk));
            }
            foreach($followers as $follower){
                Notification::send($follower,new CommentedOnShortOpinion($opinion,$comment,Auth::user()->user,$fcm_tokens));
            }

        }catch(\Exception $e){}
    }

    public function report(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'comment_id'=>'required',
                'flag'=>'required'
            ]);

            if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
                    //var_dump($request->input('user_id'));
                    $flag_reason=explode("--",$request->input('flag'));
                    $report_comment=new ReportComment();
                    //$report_comment->user_id=$request->input('reportuser');
                    $report_comment->short_comment_id=$request->input('comment_id');
                    $report_comment->post_comment_id="";
                    $report_comment->reported_user_id=Auth::user()->user_id;
                    $report_comment->report_flag=(int)$flag_reason[0];
                    $report_comment->report_reason=$flag_reason[1];
                    $report_comment->save();

                    $response=array('status'=>'success','result'=>1,'message'=>'Issue successfully reported.');
                    return response()->json($response,200);
           }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    protected function is_active_mode_changer($comment_id,$is_active,$event){
        DB::transaction(function () use($comment_id,$is_active,$event){
            DB::table('report_comment')->where('comment_id', '=', $user_id)->update(['is_active' => $is_active]);
        });
    }

}
