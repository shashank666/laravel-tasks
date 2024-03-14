<?php

namespace App\Http\Controllers\Api\Post;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Model\Comment;
use App\Model\CommentLike;
use App\Model\Post;
use App\Model\UserDevice;
use DB;
use Carbon\Carbon;
use Notification;
use App\Notifications\Frontend\CommentedOnPost;
use App\Jobs\AndroidPush\CommentedOnPostJob;

class CommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api',['except'=>['show','replies']]);
    }

    public function show(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'post_id'=>'required'
            ]);
            if($validator->fails()) {
                    $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                    return response()->json($response, 200);
            }else{
                $post_id=$request->input('post_id');
                $post_exists=Post::where(['is_active'=>1,'status'=>1,'id'=>$post_id])->exists();
                if($post_exists){

                    $user_id=-1;
                    $my_liked_commentids=[];
                    $my_disagreed_commentids=[];

                    if($request->header('Authorization')){
                        $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                        $my_liked_commentids=$this->my_liked_commentids($user_id);
                        $my_disagreed_commentids=$this->my_disagreed_opinion_commentids($user_id);
                    }

                    $comments=Comment::where(['parent_id'=>0,'is_active'=>1,'post_id'=>$post_id])->with('user')->withCount('likes','replies')->orderBy('created_at','desc')->paginate(12);
                    $formatted_comments=$comments->getCollection()->transform(function($comment,$key) use($my_liked_commentids,$my_disagreed_commentids){
                        return $this->formatted_comment($comment,$my_liked_commentids,$my_disagreed_commentids);
                    });
                    $meta=$this->get_meta($comments);
                    $response=array('status'=>'success','result'=>1,'comments'=>$formatted_comments,'meta'=>$meta);
                    return response()->json($response, 200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'Article not found');
                    return response()->json($response, 200);
                }
            }
         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function replies(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'comment_id'=>'required',
            ]);
            if($validator->fails()) {
                    $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                    return response()->json($response, 200);
            }else{
                $comment_id=$request->input('comment_id');
                $comment_exists=Comment::where(['is_active'=>1,'id'=>$comment_id])->exists();
                if($comment_exists){
                    $user_id=-1;
                    $my_liked_commentids=[];

                    if($request->header('Authorization')){
                        $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                        $my_liked_commentids=$this->my_liked_commentids($user_id);
                    }
                    $comments=Comment::where(['parent_id'=>$comment_id,'is_active'=>1])->with('user')->withCount('likes','replies')->orderBy('created_at','desc')->paginate(12);
                    $formatted_comments=$comments->getCollection()->transform(function($comment,$key) use($my_liked_commentids){
                        return $this->formatted_comment($comment,$my_liked_commentids);
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
                'post_id'=>'required',
                'comment'=>'required_without:media',
                'media'=>'required_without:comment',
            ]);

            if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
            $post=Post::where(['id'=>$request->input('post_id'),'is_active'=>1])->with('user')->first();
            if($post){
                $comment=new Comment();
                if($request->hasFile('media')){
                    $original_name=$request->file('media')->getClientOriginalName();
                    $original_size=$request->file('media')->getSize();
                    $extension=$request->file('media')->getClientOriginalExtension();
                    if($original_size>5048576){
                        $response=array('status'=>'error','result'=>0,'errors'=>'Image is larger than 5 MB');
                        return response()->json($response, 200);
                    }else if(!in_array(strtolower($extension),['jpg','png','jpeg','gif'])){
                        $response=array('status'=>'error','result'=>0,'errors'=>'Image format is invalid');
                        return response()->json($response, 200);
                    }else{
                        $uniqueid=uniqid();
                        $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                        $imagepath=url('/storage/comments/'.$filename);
                        $path=$request->file('media')->storeAs('public/comments',$filename);
                        $size=$this->optimize_image($extension,'comments',$filename,$original_size);
                        $this->save_file_to_db($uniqueid,$imagepath,$filename,$original_name,'POST_COMMENT',$size,$extension,Auth::user()->user_id);
                        $request->request->add(['image' => $imagepath]);
                    }
                }
                $comment=$this->save_comment($comment,$request->all());
                if($comment!=null){
                    $this->remove_null($comment);
                    $this->notify_followers($post,$comment,'commented');
                    $response=array('status'=>'success','result'=>1,'comment'=>$comment);
                    return response()->json($response,200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'Failed to comment');
                    return response()->json($response,200);
                }
            }else{
                $response=array('status'=>'error','result'=>0,'errors'=>'Post not found');
                return response()->json($response,200);
            }
           }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }


    public function update(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'post_id'=>'required',
                'comment_id'=>'required',
                'comment'=>'required_without:media',
                'media'=>'required_without:comment',
            ]);

            if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
            $post=Post::where(['id'=>$request->input('post_id'),'is_active'=>1])->with('user')->first();
            if($post){
                $comment=Comment::where(['id'=>$request->input('comment_id'),'user_id'=>Auth::user()->user_id])->first();
                if(!empty($comment)){
                    if($request->hasFile('media')){
                        $original_name=$request->file('media')->getClientOriginalName();
                        $original_size=$request->file('media')->getSize();
                        $extension=$request->file('media')->getClientOriginalExtension();
                        if($original_size>5048576){
                            $response=array('status'=>'error','result'=>0,'errors'=>'Image is larger than 5 MB');
                            return response()->json($response, 200);
                        }else if(!in_array(strtolower($extension),['jpg','png','jpeg','gif'])){
                            $response=array('status'=>'error','result'=>0,'errors'=>'Image format is invalid');
                            return response()->json($response, 200);
                        }else{
                            $uniqueid=uniqid();
                            $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                            $imagepath=url('/storage/comments/'.$filename);
                            $path=$request->file('media')->storeAs('public/comments',$filename);
                            $size=$this->optimize_image($extension,'comments',$filename,$original_size);
                            $this->save_file_to_db($uniqueid,$imagepath,$filename,$original_name,'POST_COMMENT',$size,$extension,Auth::user()->user_id);
                            $request->request->add(['image' => $imagepath]);
                        }
                    }
                    $comment=$this->save_comment($comment,$request->all());
                    if($comment!=null){
                        $this->notify_followers($post,$comment,'updated');
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
                $response=array('status'=>'error','result'=>0,'errors'=>'Post not found');
                return response()->json($response,200);
            }
           }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function destroy(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'comment_id'=>'required',
                'post_id'=>'required'
            ]);

                if($validator->fails()) {
                        $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                        return response()->json($response, 200);
                }else{

                    $comment=Comment::where(['id'=>$request->input('comment_id'),'post_id'=>$request->input('post_id'),'user_id'=>Auth::user()->user_id])->first();
                    if($comment){
                        $comment->is_active=0;
                        $comment->save();
                        DB::table('notifications')
                            ->where('data','like','%"event":"COMMENTED_ON_ARTICLE"%')
                            ->where('data','like','%"comment_id":'.$comment->id.'%')
                            ->delete();
                        Comment::where(['parent_id'=>$request->input('comment_id'),'post_id'=>$request->input('post_id')])->update(['is_active'=>0]);
                        CommentLike::where(['comment_id'=>$comment->id])->update(['is_active'=>0]);
                        $replies=Comment::where(['parent_id'=>$comment->id,'post_id'=>$request->input('post_id')])->get();
                        foreach($replies as $reply){
                            CommentLike::where('comment_id',$reply->id)->update(['is_active',0]);
                        }
                        $total_comments=Comment::where(['post_id'=>$request->input('post_id'),'is_active'=>1])->count();
                        $response=array('status'=>'success','result'=>1,'message'=>'Comment deleted','total_comments'=>$total_comments);
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
                    $comment=Comment::where(['id'=>$comment_id,'is_active'=>1])->exists();
                    if($comment){
                        $likeFound=CommentLike::where(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id])->first();
                        if($likeFound){
                            CommentLike::where(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id])->delete();
                            $message='Comment like removed';
                        }else{
                            CommentLike::create(['user_id'=>Auth::user()->user_id,'comment_id'=>$comment_id]);
                            $message='Comment liked';
                        }
                        $count=CommentLike::where(['comment_id'=>$comment_id,'is_active'=>1])->count();
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


    protected function save_comment(Comment $comment,array $data){
        if(isset($data['parent_id']) && $data['parent_id']!=0){
            $found=Comment::where(['id'=>$data['parent_id'],'is_active'=>1])->exists();
            if(!$found){return null;}
        }
        $comment->post_id=(int)$data['post_id'];
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

    protected function notify_followers(Post $post,Comment $comment,$event)
    {
            $followers=auth()->user()->user->active_followers;
            $follower_ids=count($followers)>0?$followers->pluck('id')->toArray():[];

            if($post->user->id!==Auth::user()->user_id && !in_array($post->user->id,$follower_ids)){
                array_push($follower_ids,$post->user->id);
                $followers->push($post->user);
            }

            if($event=='updated'){
                DB::table('notifications')
                ->where('data','like','%"event":"COMMENTED_ON_ARTICLE"%')
                ->where('data','like','%"comment_id":'.$comment->id.'%')
                ->delete();
            }

            $fcm_tokens=UserDevice::select('gcm_token')->distinct()->whereIn('user_id',$follower_ids)->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
            try{
                foreach(array_chunk($fcm_tokens,100) as $chunk) {
                    dispatch(new CommentedOnPostJob($post,$comment,Auth::user()->user,$chunk));
                }
                foreach($followers as $follower){
                    Notification::send($follower,new CommentedOnPost($post,$comment,Auth::user()->user,$fcm_tokens));
                }
            }catch(\Exception $e){}
    }

}
