<?php

namespace App\Http\Controllers\Api\Polls;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
// use App\Model\ShortOpinion;
use App\Model\PollComment;
use App\Model\PollCommentLike;
use App\Model\PollCommentDisagree;
use App\Model\UserDevice;
use App\Model\Point;
use App\Model\Polls;
// use App\Model\ReportComment;

use DB;
use Carbon\Carbon;
use Notification;
// use App\Jobs\AndroidPush\CommentedOnShortOpinionJob;
use App\Jobs\AndroidPush\CommentLikeJob;
use App\Model\User;
// use App\Notifications\Frontend\CommentedOnShortOpinion;


class PollsCommentsController extends Controller
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
                $opinion_exists=Polls::where(['is_active'=>1,'id'=>$opinion_id])->exists();
            
                if($opinion_exists){

                    $user_id=-1;
                    $my_liked_commentids=[];
                    $my_disagreed_commentids=[];

                    if($request->header('Authorization')){
                        $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                        $my_liked_commentids=$this->my_liked_poll_commentids($user_id);
                        $my_disagreed_commentids=$this->my_disagreed_poll_commentids($user_id);
                    }
                    $comments=PollComment::where(['parent_id'=>0,'is_active'=>1,'poll_id'=>$opinion_id])->whereNotIn('status', [0])->with('user')->withCount('likes','disagree','replies')->orderBy('created_at','asc')->paginate(12);
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
                $opinion_exists=PollComment::where(['is_active'=>1,'id'=>$comment_id])->exists();
                if($opinion_exists){

                    $user_id=-1;
                    $my_liked_commentids=[];
                    $my_disagreed_commentids=[];

                    if($request->header('Authorization')){
                        $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                        $my_liked_commentids=$this->my_liked_poll_commentids($user_id);
                        $my_disagreed_commentids=$this->my_disagreed_poll_commentids($user_id);
                    }

                    $comments=PollComment::where(['parent_id'=>$comment_id,'is_active'=>1])->whereNotIn('status', [0])->with('user')->withCount('likes','disagree','replies')->orderBy('created_at','asc')->paginate(12);
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

    //Updating points for adding comments on opinion
    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'opinion_id'=>'required',
                'comment'=>'required',
                'comment'=>'required_without:media',
                'media'=>'required_without:comment'
            ]);

            if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            }else{
                
                $comment=new PollComment();
               
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
                    // $html_comment=(String)view('frontend.opinions.comments.comment')->with(['comment'=>$comment,'opinion_id'=>$request->input('opinion_id'),'liked_comments'=>[]]);
                    // $html_comment=(String)view('frontend.opinions.comments.comment')->with(['comment'=>$comment,'opinion_id'=>$request->input('opinion_id'),'disagreed_comments'=>[]]);
                    // $this->notify_followers($post,$comment,'commented');

                    Polls::where(['id'=>$comment->poll_id])->update(['updated_at'=>Carbon::now()]);
                    $post=Polls::where(['id'=>$comment->poll_id,'is_active'=>1])->first();
                    $response=array('status'=>'success','result'=>1,'comment'=>$comment);
                    // if($post!=null){
                    //     $this->notify_followers($post,$comment,'commented');
                    // }
                   
                    //Updating points for adding comments on opinion
                   
                    if(strlen($comment->comment)>50) {
                        $user_id = null;
                        if($request->header('Authorization')){
                            $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                        }
                        $point=Point::where(['user_id'=>$user_id])->first();
                        $user_opinion_point=Point::where(['user_id'=>$post->user_id])->first();

                        if($point==null) {
                            Point::create([
                                'user_id'=>$user_id,
                                'agree_points'=>0,
                                'comment_points'=>10,
                                'follower_points'=>0,   
                                'reward_points'=>0,
                                'post_points'=>0,
                                'share_points'=>0,
                                'daily_points'=>10
                            ]);
                        } else {
                            Point::where(['user_id'=>$user_id])->update([
                                'comment_points'=>$point->comment_points+10,
                            ]);
                            if(Carbon::now()->diffInDays($point->updated_at)==0) {
                                Point::where(['user_id'=>$user_id])->update([
                                    'daily_points'=>$point->daily_points+10,
                                ]);
                            } else {
                                Point::where(['user_id'=>$user_id])->update([
                                    'daily_points'=>10,
                                ]);
                            }
                        }
                        if($user_opinion_point==null) {
                            Point::create([
                                'user_id'=>$post->user_id,
                                'agree_points'=>0,
                                'comment_points'=>20,
                                'follower_points'=>0,   
                                'reward_points'=>0,
                                'post_points'=>0,
                                'share_points'=>0,
                                'daily_points'=>20
                            ]);
                        } else {
                            Point::where(['user_id'=>$post->user_id])->update([
                                'comment_points'=>$user_opinion_point->comment_points+20,
                            ]);
                            if(Carbon::now()->diffInDays($user_opinion_point->updated_at)==0) {
                                Point::where(['user_id'=>$post->user_id])->update([
                                    'daily_points'=>$user_opinion_point->daily_points+20,
                                ]);
                            } else {
                                Point::where(['user_id'=>$post->user_id])->update([
                                    'daily_points'=>20,
                                ]);
                            }
                        }
                    }
                    return response()->json($response,200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'Failed to comment');
                    return response()->json($response,200);
                }
           }
         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

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
                $comment=PollComment::where(['id'=>$comment_id,'is_active'=>1])->exists();
                $user= User::where(['id'=>Auth::user()->user_id])->first();
                $short_comment = PollComment::where(['id'=>$comment_id,'is_active'=>1])->first();
                 
                if($comment){
                    $users_fcm_tokens=UserDevice::select('gcm_token')->distinct()->whereIn('user_id',[$short_comment->user_id])->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
                        $likeFound=PollCommentLike::where(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id])->first();
                        $disagreeFound=PollCommentDisagree::where(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id])->first();
                        if($likeFound){
                            PollCommentLike::where(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id])->delete();
                            $message='Comment like removed';
                        }elseif($disagreeFound){
                            PollCommentDisagree::where(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id])->delete();
                            $message='Comment disagree removed';
                            PollCommentLike::create(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id]);
                            $message='Comment like';

                           
                            try{
                            dispatch(new CommentLikeJob($user,$users_fcm_tokens,$short_comment));
                            }catch(\Exception $e){
                               
                            }
                        }else{
                            PollCommentLike::create(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id]);
                            $message='Comment like';
                            dispatch(new CommentLikeJob($user,$users_fcm_tokens,$short_comment));
                               
                        }
                            // Notification::send($follower,new PollLiked($object,Auth::user(),$fcm_tokens));
                        
                        $count=PollCommentLike::where(['comment_id'=>$comment_id,'is_active'=>1])->count();
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
                $comment=PollComment::where(['id'=>$comment_id,'is_active'=>1])->exists();
                if($comment){
                        $disagreeFound=PollCommentDisagree::where(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id])->first();
                        $likeFound=PollCommentLike::where(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id])->first();
                        if($disagreeFound){
                            PollCommentDisagree::where(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id])->delete();
                            $message='Comment disagree removed';
                       }elseif($likeFound){
                            PollCommentLike::where(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id])->delete();
                            $message='Comment like removed ';
                            PollCommentDisagree::create(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id]);
                            $message='Comment disagree';
                       }else{
                            PollCommentDisagree::create(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id]);
                            $message='Comment disagree';
                        }
                        $count=PollCommentDisagree::where(['comment_id'=>$comment_id,'is_active'=>1])->count();
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

    protected function save_comment(PollComment $comment,array $data){
        if(isset($data['parent_id']) && $data['parent_id']!=0){
            $found=PollComment::where(['id'=>$data['parent_id'],'is_active'=>1])->exists();
            if(!$found){return null;}
        }
        $comment->poll_id=(int)$data['opinion_id'];
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

    protected function notify_followers(Poll $opinion,PollComment $comment,$event){
        $followers=auth()->user()->user->active_followers;
        $follower_ids=count($followers)>0?$followers->pluck('id')->toArray():[];

        if($opinion->user && $opinion->user->id!==Auth::user()->user_id && !in_array($opinion->user->id,$follower_ids)){
            array_push($follower_ids,$opinion->user->id);
            $followers->push($opinion->user);
        }
        $fcm_tokens=UserDevice::select('gcm_token')->distinct()->whereIn('user_id',$follower_ids)->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
        try{
            foreach(array_chunk($fcm_tokens,100) as $chunk) {
                dispatch(new CommentedOnPollJob($opinion,$comment,Auth::user()->user,$chunk));
            }
            foreach($followers as $follower){
                Notification::send($follower,new CommentedOnPoll($opinion,$comment,Auth::user()->user,$fcm_tokens));
            }

        }catch(\Exception $e){}
    }

    // public function report(Request $request){
    //     try{
    //         $validator = Validator::make($request->all(), [
    //             'comment_id'=>'required',
    //             'flag'=>'required'
    //         ]);

    //         if($validator->fails()) {
    //             $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
    //             return response()->json($response, 200);
    //        }else{
    //                 //var_dump($request->input('user_id'));
    //                 $flag_reason=explode("--",$request->input('flag'));
    //                 $report_comment=new ReportComment();
    //                 //$report_comment->user_id=$request->input('reportuser');
    //                 $report_comment->short_comment_id=$request->input('comment_id');
    //                 $report_comment->post_comment_id="";
    //                 $report_comment->reported_user_id=Auth::user()->user_id;
    //                 $report_comment->report_flag=(int)$flag_reason[0];
    //                 $report_comment->report_reason=$flag_reason[1];
    //                 $report_comment->save();

    //                 $response=array('status'=>'success','result'=>1,'message'=>'Issue successfully reported.');
    //                 return response()->json($response,200);
    //        }
    //     }catch(\Exception $e){
    //         $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
    //         return response()->json($response, 500);
    //     }
    // }

    protected function is_active_mode_changer($comment_id,$is_active,$event){
        DB::transaction(function () use($comment_id,$is_active,$event){
            DB::table('report_comment')->where('comment_id', '=', $user_id)->update(['is_active' => $is_active]);
        });
    }

}