<?php

namespace App\Http\Controllers\Frontend\Opinion;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Model\ThreadFollower;
use App\Model\Thread;
use App\Model\User;
use App\Model\UserDevice;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionComment;
use App\Model\ShortOpinionCommentLike;
use App\Events\OpinionViewCounterEvent;
use App\Model\ShortOpinionCommentDisagree;
use App\Notifications\Frontend\CommentedOnShortOpinion;
use App\Jobs\AndroidPush\CommentedOnShortOpinionJob;
use DB;
use Notification;
use Carbon\Carbon;

class ShortOpinionCommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',['except'=>['load','replies']]);
    }

    public function load(Request $request){
        $validator = Validator::make($request->all(), [
            'opinion_id'=>'required'
        ]);

        if($validator->fails()){
            return response()->json(array('status'=>'error','message'=>'Input missing'));
        }else{

            $query = ShortOpinionComment::query();
            $query->where(['short_opinion_id'=>$request->query('opinion_id'),'parent_id'=>0,'is_active'=>1])->whereNotIn('status', [0])->withCount('likes','disagree','replies')->with('user');
            $query->where(['short_opinion_id'=>$request->query('opinion_id'),'parent_id'=>0,'is_active'=>1])->whereNotIn('status', [0])->withCount('disagree','replies')->with('user');

            $limit=$request->has('limit') && $request->query('limit')>0?$request->query('limit'):20;
            $query->orderBy('created_at','asc');
            $comments=$query->paginate($limit);
            $liked_comments=Auth::check()?$this->my_liked_opinion_commentids(Auth::user()->id):[];
            $disagreed_comments=Auth::check()?$this->my_disagreed_opinion_commentids(Auth::user()->id):[];

            $html_comments=(String) view('frontend.opinions.comments.comments_loop')->with(['comments'=>$comments,'opinion_id'=>$request->query('opinion_id'),'liked_comments'=>$liked_comments,'disagreed_comments'=>$disagreed_comments]);
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
            'opinion_id'=>'required'
        ]);

        if($validator->fails()){
            return response()->json(array('status'=>'error','message'=>'Input missing'));
        }else{
            $query = ShortOpinionComment::query();
            $limit=$request->has('limit') && $request->query('limit')>0?$request->query('limit'):20;
            $query->where(['parent_id'=>$request->input('comment_id'),'is_active'=>1])->whereNotIn('status', [0])->withCount('likes','replies')->with('user');
            $query->where(['parent_id'=>$request->input('comment_id'),'is_active'=>1])->whereNotIn('status', [0])->withCount('disagree','replies')->with('user');
            $query->orderBy('created_at','asc');
            $replies=$query->paginate($limit);
            $liked_comments=Auth::check()?$this->my_liked_opinion_commentids(Auth::user()->id):[];
            $disagreed_comments=Auth::check()?$this->my_disagreed_opinion_commentids(Auth::user()->id):[];
            $html_replies=(String) view('frontend.opinions.comments.replies_loop')->with(['parent_id'=>$request->input('comment_id'),'replies'=>$replies,'opinion_id'=>$request->query('opinion_id'),'liked_comments'=>$liked_comments,'disagreed_comments'=>$disagreed_comments]);
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
            'opinion_id'=>'required',
            'comment'=>'nullable|required_without_all:comment_media,comment_image',
            'comment_media'=>'nullable|mimes:jpeg,png,gif,jpg|file|max:2050|required_without_all:comment,comment_image',
            'comment_image'=>'nullable|url|required_without_all:comment,comment_media',
        ]);

        if($validator->fails()){
            return response()->json(array('status'=>'error','message'=>'Comment can not empty'));
        }else{
            $post=ShortOpinion::where(['id'=>$request->input('opinion_id'),'is_active'=>1])->first();
            if($post){
                $total_comments=ShortOpinionComment::where(['short_opinion_id'=>$request->input('opinion_id'),'is_active'=>1])->count();
                if($request->hasFile('comment_media')){
                    $original_name=$request->file('comment_media')->getClientOriginalName();
                    $original_size=$request->file('comment_media')->getSize();
                    $extension=$request->file('comment_media')->getClientOriginalExtension();
                    $uniqueid=uniqid();
                    $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                    $imagepath='https://d20g1jo8qvj2jf.cloudfront.net/storage/app/public/comments/'.$filename;
                    $path=$request->file('comment_media')->storeAs('public/comments',$filename);
                    $size=$this->optimize_image($extension,'comments',$filename,$original_size);
                    $this->save_file_to_db($uniqueid,'storage/app/public/comments/'.$filename,$filename,$original_name,'OPINION_COMMENT',$size,$extension,Auth::user()->id);
                    $request->request->add(['image' => $imagepath]);
                }else if($request->has('comment_image') && $request->input('comment_image')!=null){
                    $request->request->add(['image' => $request->input('comment_image')]);
                }else{
                    $request->request->add(['image' => NULL]);
                }
                try{
                $comment=$this->save_comment(new ShortOpinionComment(),$request->all());
                if($comment!=null){
                    $comment->user=Auth::user();
                    $comment->likes_count=0;
                    $comment->disagree_count=0;
                    $comment->replies_count=0;
                    $html_comment=(String)view('frontend.opinions.comments.comment')->with(['comment'=>$comment,'opinion_id'=>$request->input('opinion_id'),'liked_comments'=>[]]);
                    $html_comment=(String)view('frontend.opinions.comments.comment')->with(['comment'=>$comment,'opinion_id'=>$request->input('opinion_id'),'disagreed_comments'=>[]]);
                    $this->notify_followers($post,$comment,'commented');
                    ShortOpinion::where(['id'=>$post->id])->update(['last_updated_at'=>Carbon::now()]);
                    return response()->json(array('status'=>'success','message'=>'comment added','comment'=>$html_comment,'total_comments'=>$total_comments));
                }else{
                    return response()->json(array('status'=>'error','message'=>'Comment you want to reply has been delete by user'));
                }

            }catch(\Exception $e){
                return response()->json(array('status'=>'error','message'=>'Internal Error '.$e.get_message()));
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
            'opinion_id'=>'required',
            'comment'=>'nullable|required_without_all:comment_media,comment_image',
            'comment_media'=>'nullable|mimes:jpeg,png,gif,jpg|file|max:2050|required_without_all:comment,comment_image',
            'comment_image'=>'nullable|url|required_without_all:comment,comment_media',
        ]);

        if($validator->fails()){
            return response()->json(array('status'=>'error','message'=>'Comment can not empty'));
        }else{
            $comment=ShortOpinionComment::where(['id'=>$request->input('comment_id'),'is_active'=>1,'user_id'=>Auth::user()->id])->withCount('likes','replies')->with('user')->first();
            $comment1=ShortOpinionComment::where(['id'=>$request->input('comment_id'),'is_active'=>1,'user_id'=>Auth::user()->id])->withCount('disagree','replies')->with('user')->first();
            if($comment && $comment1){
                if($request->hasFile('comment_media')){
                    $original_name=$request->file('comment_media')->getClientOriginalName();
                    $original_size=$request->file('comment_media')->getSize();
                    $extension=$request->file('comment_media')->getClientOriginalExtension();
                    $uniqueid=uniqid();
                    $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                    $imagepath=url('/storage/comments/'.$filename);
                    $path=$request->file('comment_media')->storeAs('public/comments',$filename);
                    $size=$this->optimize_image($extension,'comments',$filename,$original_size);
                    $this->save_file_to_db($uniqueid,$imagepath,$filename,$original_name,'OPINION_COMMENT',$size,$extension,Auth::user()->id);
                    $request->request->add(['image' => $imagepath]);
                }else if($request->has('comment_image') && $request->input('comment_image')!=null){
                    $request->request->add(['image' => $request->input('comment_image')]);
                }else{
                    $request->request->add(['image' => NULL]);
                }
                $comment=$this->save_comment($comment,$request->all());
                if($comment!=null){
                    $opinion=ShortOpinion::where('id',$request->opinion_id)->first();
                    $liked_comments=Auth::check()?$this->my_liked_commentids(Auth::user()->id):[];
                    $disagreed_comments=Auth::check()?$this->my_disagreed_commentids(Auth::user()->id):[];
                    $html_comment=(String)view('frontend.opinions.comments.comment')->with(['comment'=>$comment,'opinion_id'=>$request->input('opinion_id'),'liked_comments'=>$liked_comments,'disagreed_comments'=>$disagreed_comments] AND ['comment'=>$comment,'opinion_id'=>$request->input('opinion_id')]);
                    $this->notify_followers($opinion,$comment,'updated');
                    ShortOpinion::where(['id'=>$opinion->id])->update(['last_updated_at'=>Carbon::now()]);
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
            'opinion_id'=>'required'
        ]);

        if($validator->fails()){
            return response()->json(array('status'=>'error','message'=>'Input missing'));
        }else{

            $comment=ShortOpinionComment::where(['id'=>$request->comment_id,'short_opinion_id'=>$request->opinion_id,'user_id'=>auth()->user()->id])->first();
            if($comment){
                DB::table('notifications')
                ->where('data','like'||'disagree','%"event":"COMMENTED_ON_OPINION"%')
                ->where('data','like'||'disagree','%"comment_id":'.$comment->id.'%')
                ->delete();
                $comment->is_active=0;
                $comment->save();
                ShortOpinionComment::where(['parent_id'=>$request->comment_id,'short_opinion_id'=>$request->opinion_id])->update(['is_active'=>0]);
                ShortOpinionCommentLike::where(['comment_id'=>$comment->id])->update(['is_active'=>0]);
                ShortOpinionCommentDisagree::where(['comment_id'=>$comment->id])->update(['is_active'=>0]);
                $replies=ShortOpinionComment::where(['parent_id'=>$request->comment_id,'short_opinion_id'=>$request->opinion_id])->get();
                foreach($replies as $reply){
                    ShortOpinionCommentLike::where('comment_id',$reply->id)->update(['is_active' => 0]);
                    ShortOpinionCommentDisagree::where('comment_id',$reply->id)->update(['is_active' => 0]);
                }
                $total_comments=ShortOpinionComment::where(['short_opinion_id'=>$request->input('opinion_id'),'is_active'=>1])->count();
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
            $comment=ShortOpinionComment::where(['id'=>$comment_id,'is_active'=>1])->exists();
            if($comment){
                $likeFound=ShortOpinionCommentLike::where(['user_id'=>Auth::user()->id,'comment_id'=>$comment_id])->first();
                $disagreeFound=ShortOpinionCommentDisagree::where(['user_id'=>Auth::user()->id,'comment_id'=>$comment_id])->first();
                if($likeFound){
                    ShortOpinionCommentLike::where(['user_id'=>Auth::user()->id,'comment_id'=>$comment_id])->delete();
                    $status='like';
                }elseif($disagreeFound){
                    ShortOpinionCommentDisagree::where(['user_id'=>Auth::user()->id,'comment_id'=>$comment_id])->delete();
                    $status='disagree';
                    ShortOpinionCommentLike::create(['user_id'=>Auth::user()->id,'comment_id'=>$comment_id]);
                    $status='liked';
                }else{
                    ShortOpinionCommentLike::create(['user_id'=>Auth::user()->id,'comment_id'=>$comment_id]);
                    $status='liked';
                }
                $count=ShortOpinionCommentLike::where(['comment_id'=>$comment_id])->count();
                $response=array('status'=>$status,'count'=>$count);
                return response()->json($response);
            }else{
                $response=array('status'=>'error','message'=>'Comment not found');
                return response()->json($response, 200);
            }
        }
    }

    public function like_opinion(Request $request)
    {
        $opinion_id=$request->input('opinion_id');
        $Agree_Disagree=$request->input('Agree_Disagree');
        $short_opinion=ShortOpinion::where(['id'=>$opinion_id,'is_active'=>1])->with('user')->first();
        $Liked=DB::table('short_opinion_likes')->where(['user_id'=>Auth::user()->id,'short_opinion_id'=>$opinion_id])->first();
        if($Liked){
            if($Agree_Disagree==$Liked->Agree_Disagree){
                //Removing Agree-Disagree
                Auth::user()->likes()->detach($opinion_id);
                // DB::table('notifications')
                // ->where('data','like','%"event":"OPINION_LIKED"%')
                // ->where('data','like','%"opinion_id":'.$opinion_id.'%')
                // ->where('data','like','%"sender_id":'.Auth::user()->id.'%')
                // ->delete();
                if($request->ajax()){
                $response=array('status'=>'like');
                return response()->json($response);
                }
            }else{
                Auth::user()->likes()->detach($opinion_id);
                Auth::user()->likes()->attach($opinion_id);
                DB::table('short_opinions')->where(['id'=>$short_opinion->id])->update(['last_updated_at'=>Carbon::now()]);
                DB::table('short_opinion_likes')->where(['short_opinion_id'=>$opinion_id, 'user_id'=>Auth::user()->id])->update(['Agree_Disagree'=>$Agree_Disagree]);
                // $this->notify_followers($short_opinion,'ShortOpinionLiked');
                if($request->ajax()){
                    $response=array('status'=>'liked','Agree_Disagree'=>$Agree_Disagree);
                    return response()->json($response);
                }
            }
        }else{
            Auth::user()->likes()->attach($opinion_id);
            DB::table('short_opinions')->where(['id'=>$short_opinion->id])->update(['last_updated_at'=>Carbon::now()]);
            DB::table('short_opinion_likes')->where(['short_opinion_id'=>$opinion_id, 'user_id'=>Auth::user()->id])->update(['Agree_Disagree'=>$Agree_Disagree]);
            // $this->notify_followers($short_opinion,'ShortOpinionLiked');
            if($request->ajax()){
                $response=array('status'=>'liked','Agree_Disagree'=>$Agree_Disagree);
                return response()->json($response);
             }
        }
    }

    public function disagree(Request $request){
        $validator = Validator::make($request->all(),[
            'comment_id'=>'required',
        ]);
        if($validator->fails()){
            return response()->json(array('status'=>'error','message'=>'Input missing'));
        }else{
            $comment_id=$request->input('comment_id');
            $comment=ShortOpinionComment::where(['id'=>$comment_id,'is_active'=>1])->exists();
            if($comment){
                $disagreeFound=ShortOpinionCommentDisagree::where(['user_id'=>Auth::user()->id,'comment_id'=>$comment_id])->first();
                $likeFound=ShortOpinionCommentLike::where(['user_id'=>Auth::user()->id,'comment_id'=>$comment_id])->first();
                if($disagreeFound){
                    ShortOpinionCommentDisagree::where(['user_id'=>Auth::user()->id,'comment_id'=>$comment_id])->delete();
                    $status='disagree';
                }elseif($likeFound){
                    ShortOpinionCommentLike::where(['user_id'=>Auth::user()->id,'comment_id'=>$comment_id])->delete();
                    $status='like';
                    ShortOpinionCommentDisagree::create(['user_id'=>Auth::user()->id,'comment_id'=>$comment_id]);
                    $status='disagreed';
                }else{
                    ShortOpinionCommentDisagree::create(['user_id'=>Auth::user()->id,'comment_id'=>$comment_id]);
                    $status='disagreed';
                }
                $count=ShortOpinionCommentDisagree::where(['comment_id'=>$comment_id])->count();
                $response=array('status'=>$status,'count'=>$count);
                return response()->json($response);
            }else{
                $response=array('status'=>'error','message'=>'Comment not found');
                return response()->json($response, 200);
            }
        }
    }


    protected function save_comment(ShortOpinionComment $comment,array $data){
        if($data['parent_id']!=0){
            $found=ShortOpinionComment::where(['id'=>$data['parent_id'],'is_active'=>1])->exists();
            if(!$found){return null;}
        }
        $comment->short_opinion_id=(int)$data['opinion_id'];
        $comment->user_id=Auth::user()->id;
        $comment->parent_id=isset($data['parent_id'])?$data['parent_id']:0;
        $comment->comment=isset($data['comment'])?$data['comment']:NULL;
        $comment->media=isset($data['image'])?$data['image']:NULL;
        $comment->save();
        return $comment;
    }

    protected function notify_followers(ShortOpinion $opinion,ShortOpinionComment $comment,$event){
        $followers=auth()->user()->active_followers;
        $follower_ids=count($followers)>0?$followers->pluck('id')->toArray():[];

        if($opinion->user && $opinion->user->id!==Auth::user()->id && !in_array($opinion->user->id,$follower_ids)){
            array_push($follower_ids,$opinion->user->id);
            $followers->push($opinion->user);
        }
        $fcm_tokens=UserDevice::select('gcm_token')->distinct()->whereIn('user_id',$follower_ids)->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
        try{
            foreach(array_chunk($fcm_tokens,100) as $chunk) {
                dispatch(new CommentedOnShortOpinionJob($opinion,$comment,Auth::user(),$chunk));
            }
            foreach($followers as $follower){
                Notification::send($follower,new CommentedOnShortOpinion($opinion,$comment,Auth::user(),$fcm_tokens));
            }
        }catch(\Exception $e){}
    }
    
     // function for follow thread
     public function follow_thread(Request $request){
        $thread=Thread::where(['id'=>$request->input('id'),'is_active'=>1])->first();
        $thread_id=$thread->id;
        if($thread){
            $Followed=ThreadFollower::where(['user_id'=>Auth::user()->id,'thread_id'=>$thread_id])->exists();
            if($Followed){
                Auth::user()->followed_thread()->detach($thread_id);
                $status='follow';
            }else{
                Auth::user()->followed_thread()->attach($thread_id);
                $status='followed';
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

    
   
}
