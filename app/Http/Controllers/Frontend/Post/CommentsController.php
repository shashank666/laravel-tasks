<?php

namespace App\Http\Controllers\Frontend\Post;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Model\User;
use App\Model\UserDevice;
use App\Model\Post;
use App\Model\Comment;
use App\Model\CommentLike;
use DB;
use Carbon\Carbon;
use Notification;
use App\Notifications\Frontend\CommentedOnPost;
use App\Jobs\AndroidPush\CommentedOnPostJob;

class CommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',['except'=>['load','replies']]);
    }

    public function load(Request $request){

        $validator = Validator::make($request->all(), [
            'post_id'=>'required'
        ]);

        if($validator->fails()){
            return response()->json(array('status'=>'error','message'=>'Input missing'));
        }else{

            $query = Comment::query();
            $query->where(['post_id'=>$request->query('post_id'),'parent_id'=>0,'is_active'=>1])->withCount('likes','replies')->with('user');
            $limit=$request->has('limit') && $request->query('limit')>0?$request->query('limit'):12;
            $query->orderBy('created_at','desc');
            $comments=$query->paginate($limit);
            $liked_comments=Auth::check()?$this->my_liked_commentids(Auth::user()->id):[];
            $html_comments=(String) view('frontend.posts.comments.comments_loop')->with(['comments'=>$comments,'post_id'=>$request->query('post_id'),'liked_comments'=>$liked_comments]);
            $response=array('html'=>$html_comments);
            if($request->ajax()){
                return response()->json($response);
            }else{
                return redirect('/');
            }
        }
    }

    public function replies(Request $request){

        $validator = Validator::make($request->all(), [
            'comment_id'=>'required',
            'post_id'=>'required'
        ]);

        if($validator->fails()){
            return response()->json(array('status'=>'error','message'=>'Input missing'));
        }else{
            $query = Comment::query();
            $limit=$request->has('limit') && $request->query('limit')>0?$request->query('limit'):4;
            $query->where(['parent_id'=>$request->input('comment_id'),'is_active'=>1])->withCount('likes','replies')->with('user');
            $query->orderBy('created_at','desc');
            $replies=$query->paginate($limit);
            $liked_comments=Auth::check()?$this->my_liked_commentids(Auth::user()->id):[];
            $html_replies=(String) view('frontend.posts.comments.replies_loop')->with(['parent_id'=>$request->input('comment_id'),'replies'=>$replies,'post_id'=>$request->query('post_id'),'liked_comments'=>$liked_comments]);
            $response=array('html'=>$html_replies);
            if($request->ajax()){
                return response()->json($response);
            }else{
                return redirect('/');
            }
        }
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'post_id'=>'required',
            'comment'=>'nullable|required_without_all:comment_media,comment_image',
            'comment_media'=>'nullable|mimes:jpeg,png,gif,jpg|file|max:2050|required_without_all:comment,comment_image',
            'comment_image'=>'nullable|url|required_without_all:comment,comment_media',
        ]);

        if($validator->fails()){
            return response()->json(array('status'=>'error','message'=>'Comment can not empty','errors'=>implode(',',$validator->errors()->all())));
        }else{

            $post=Post::where(['id'=>$request->input('post_id'),'is_active'=>1])->first();
            if($post){
                $total_comments=Comment::where(['post_id'=>$request->input('post_id'),'is_active'=>1])->count();
                if($request->hasFile('comment_media')){
                    $original_name=$request->file('comment_media')->getClientOriginalName();
                    $original_size=$request->file('comment_media')->getSize();
                    $extension=$request->file('comment_media')->getClientOriginalExtension();
                    $uniqueid=uniqid();
                    $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                    $imagepath=url('/storage/comments/'.$filename);
                    $path=$request->file('comment_media')->storeAs('public/comments',$filename);
                    $size=$this->optimize_image($extension,'comments',$filename,$original_size);
                    $this->save_file_to_db($uniqueid,$imagepath,$filename,$original_name,'POST_COMMENT',$size,$extension,Auth::user()->id);
                    $request->request->add(['image' => $imagepath]);
                }else if($request->has('comment_image') && $request->input('comment_image')!=null){
                    $request->request->add(['image' => $request->input('comment_image')]);
                }else{
                    $request->request->add(['image' => NULL]);
                }

                $comment=$this->save_comment(new Comment(),$request->all());
                if($comment!=null){
                    $comment->user=Auth::user();
                    $comment->likes_count=0;
                    $comment->replies_count=0;
                    $html_comment=(String)view('frontend.posts.comments.comment')->with(['comment'=>$comment,'post_id'=>$request->input('post_id'),'liked_comments'=>[]]);
                    $this->notify_followers($post,$comment,'commented');
                    return response()->json(array('status'=>'success','message'=>'comment added','comment'=>$html_comment,'total_comments'=>$total_comments));
                }else{
                    return response()->json(array('status'=>'error','message'=>'Comment you want to reply has been delete by user'));
                }
            }else{
                return response()->json(array('status'=>'error','message'=>'post not found'));
            }
        }
    }


    public function update(Request $request){

        $validator = Validator::make($request->all(), [
            'comment_id'=>'required',
            'parent_id'=>'required',
            'post_id'=>'required',
            'comment'=>'nullable|required_without_all:comment_media,comment_image',
            'comment_media'=>'nullable|mimes:jpeg,png,gif,jpg|file|max:2050|required_without_all:comment,comment_image',
            'comment_image'=>'nullable|url|required_without_all:comment,comment_media',
        ]);

        if($validator->fails()){
            return response()->json(array('status'=>'error','message'=>'Comment can not empty'));
        }else{

            $comment=Comment::where(['id'=>$request->input('comment_id'),'is_active'=>1,'user_id'=>Auth::user()->id])->withCount('likes','replies')->with('user')->first();
            if($comment){
                if($request->hasFile('comment_media')){
                    $original_name=$request->file('comment_media')->getClientOriginalName();
                    $original_size=$request->file('comment_media')->getSize();
                    $extension=$request->file('comment_media')->getClientOriginalExtension();
                    $uniqueid=uniqid();
                    $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                    $imagepath=url('/storage/comments/'.$filename);
                    $path=$request->file('comment_media')->storeAs('public/comments',$filename);
                    $size=$this->optimize_image($extension,'comments',$filename,$original_size);
                    $this->save_file_to_db($uniqueid,$imagepath,$filename,$original_name,'POST_COMMENT',$size,$extension,Auth::user()->id);
                    $request->request->add(['image' => $imagepath]);
                }else if($request->has('comment_image') && $request->input('comment_image')!=null){
                    $request->request->add(['image' => $request->input('comment_image')]);
                }else{
                    $request->request->add(['image' => NULL]);
                }
                $comment=$this->save_comment($comment,$request->all());
                if($comment!=null){
                    $post=Post::where('id',$request->post_id)->first();
                    $liked_comments=Auth::check()?$this->my_liked_commentids(Auth::user()->id):[];
                    $html_comment=(String)view('frontend.posts.comments.comment')->with(['comment'=>$comment,'post_id'=>$request->input('post_id'),'liked_comments'=>$liked_comments]);
                    $this->notify_followers($post,$comment,'updated');
                    return response()->json(array('status'=>'success','message'=>'comment updated','comment'=>$html_comment));
                }else{
                    return response()->json(array('status'=>'error','message'=>'Comment you want to reply has been delete by user'));
                }
            }else{
                return response()->json(array('status'=>'error','message'=>'Comment not found'));
            }
        }
    }

    public function destroy(Request $request){

        $validator = Validator::make($request->all(), [
            'comment_id'=>'required',
            'post_id'=>'required'
        ]);

        if($validator->fails()){
            return response()->json(array('status'=>'error','message'=>'Input missing'));
        }else{
            $comment=Comment::where(['id'=>$request->comment_id,'post_id'=>$request->post_id,'user_id'=>auth()->user()->id,'is_active'=>1])->first();
            if($comment){
                DB::table('notifications')
                ->where('data','like','%"event":"COMMENTED_ON_ARTICLE"%')
                ->where('data','like','%"comment_id":'.$comment->id.'%')
                ->delete();
                $comment->is_active=0;
                $comment->save();
                Comment::where(['parent_id'=>$request->comment_id,'post_id'=>$request->post_id])->update(['is_active'=>0]);
                CommentLike::where(['comment_id'=>$comment->id])->update(['is_active'=>0]);
                $replies=Comment::where(['parent_id'=>$request->comment_id,'post_id'=>$request->post_id])->get();
                foreach($replies as $reply){
                    CommentLike::where('comment_id',$reply->id)->update(['is_active' => 0]);
                }
                $total_comments=Comment::where(['post_id'=>$request->input('post_id'),'is_active'=>1])->count();
                return response()->json(array('status'=>'success','message'=>'comment deleted','total_comments'=>$total_comments));
            }else{
                return response()->json(array('status'=>'error','message'=>'comment not found'));
            }
        }
    }

    public function like(Request $request){
        $validator = Validator::make($request->all(),[
            'comment_id'=>'required',
        ]);
        if($validator->fails()){
            return response()->json(array('status'=>'error','message'=>'Input missing'));
        }else{
            $comment_id=$request->input('comment_id');
            $comment=Comment::where(['id'=>$comment_id,'is_active'=>1])->exists();
            if($comment){
            $likeFound=CommentLike::where(['user_id'=>Auth::user()->id,'comment_id'=>$comment_id])->first();
                if($likeFound){
                    CommentLike::where(['user_id'=>Auth::user()->id,'comment_id'=>$comment_id])->delete();
                    $status='like';
                }else{
                    CommentLike::create(['user_id'=>Auth::user()->id,'comment_id'=>$comment_id]);
                    $status='liked';
                }
                $count=CommentLike::where(['comment_id'=>$comment_id])->count();
                $response=array('status'=>$status,'count'=>$count);
                return response()->json($response);
            }else{
                $response=array('status'=>'error','message'=>'Comment not found');
                return response()->json($response, 200);
            }
        }
    }


    protected function save_comment(Comment $comment,array $data){
        if($data['parent_id']!=0){
            $found=Comment::where(['id'=>$data['parent_id'],'is_active'=>1])->exists();
            if(!$found){return null;}
        }
        $comment->post_id=(int)$data['post_id'];
        $comment->user_id=Auth::user()->id;
        $comment->parent_id=isset($data['parent_id'])?$data['parent_id']:0;
        $comment->comment=isset($data['comment'])?$data['comment']:NULL;
        $comment->media=isset($data['image'])?$data['image']:NULL;
        $comment->save();
        return $comment;
    }


    protected function notify_followers(Post $post,Comment $comment,$event)
    {
        $followers=auth()->user()->active_followers;
        $follower_ids=count($followers)>0?$followers->pluck('id')->toArray():[];

        if($post->user && $post->user->id!==Auth::user()->id && !in_array($post->user->id,$follower_ids)){
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
                dispatch(new CommentedOnPostJob($post,$comment,Auth::user(),$chunk));
            }
            foreach($followers as $follower){
                    Notification::send($follower,new CommentedOnPost($post,$comment,Auth::user(),$fcm_tokens));
            }
        }catch(\Exception $e){}
    }



}
