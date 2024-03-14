<?php

namespace App\Http\Controllers\Admin\Opinion;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Model\User;
use App\Model\UserDevice;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionComment;
use App\Model\ShortOpinionCommentLike;
use App\Notifications\Frontend\CommentedOnShortOpinion;
use App\Jobs\AndroidPush\CommentedOnShortOpinionJob;
use DB;
use Notification;
use Carbon\Carbon;

class OpinionsCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

    }

    public function load(Request $request){
        $validator = Validator::make($request->all(), [
            'opinion_id'=>'required'
        ]);

        if($validator->fails()){
            return response()->json(array('status'=>'error','message'=>'Input missing'));
        }else{

            $query = ShortOpinionComment::query();
            $query->where(['short_opinion_id'=>$request->query('opinion_id'),'parent_id'=>0])->withCount('likes','totalreplies')->with('user');
            $limit=$request->has('limit') && $request->query('limit')>0?$request->query('limit'):3;
            $query->orderBy('created_at','desc');
            $comments=$query->paginate($limit);
            $liked_comments=Auth::check()?$this->my_liked_opinion_commentids(Auth::user()->id):[];

            $html_comments=(String) view('admin.dashboard.opinion.comments.comments_loop')->with(['comments'=>$comments,'opinion_id'=>$request->query('opinion_id'),'liked_comments'=>$liked_comments]);
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
            $query->where(['short_opinion_id'=>$request->query('opinion_id'),'parent_id'=>$request->input('comment_id')])->withCount('likes','totalreplies')->with('user');
            $limit=$request->has('limit') && $request->query('limit')>0?$request->query('limit'):3;
            $query->orderBy('created_at','desc');
            $replies=$query->paginate($limit);
            $liked_comments=Auth::check()?$this->my_liked_opinion_commentids(Auth::user()->id):[];
            $html_replies=(String) view('admin.dashboard.opinion.comments.replies_loop')->with(['parent_id'=>$request->input('comment_id'),'replies'=>$replies,'opinion_id'=>$request->query('opinion_id'),'liked_comments'=>$liked_comments]);
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

                $admin_id =  Auth::guard('admin')->user()->id;
                $gender_op = $request->input('gender');
                $user_is = User::whereBetween('id',[4406,4805])->inRandomOrder()->first();
                // if($admin_id==4){
                //     if($gender_op!=null){
                //         $user_is = User::where(['gender'=>$gender_op])->whereBetween('id',[4406,4605])->inRandomOrder()->first();
                //     }
                //     else{
                //         $user_is = User::whereBetween('id',[4406,4605])->inRandomOrder()->first();
                //     }
                //   //$user_id_test = rand(4406,4505);
                // }
                // elseif($admin_id==5){
                //   if($gender_op!=null){
                //         $user_is = User::where(['gender'=>$gender_op])->whereBetween('id',[4506,4605])->inRandomOrder()->first();
                //     }
                //     else{

                //         $user_is = User::whereBetween('id',[4506,4605])->inRandomOrder()->first();
                //     }
                // }
                // elseif($admin_id==6){
                //     if($gender_op!=null){
                //         $user_is = User::where(['gender'=>$gender_op])->whereBetween('id',[4606,4705])->inRandomOrder()->first();
                //     }
                //     else{
                //         $user_is = User::whereBetween('id',[4606,4705])->inRandomOrder()->first();
                //     }
                  
                // }
                
                //var_dump($user_test);

              /*
                if($admin_id==4){
                  $user_is = rand(4406,4505);
                }
                elseif($admin_id==5){
                  $user_is = rand(4506,4605);
                }
                elseif($admin_id==6){
                  $user_is = rand(4606,4705);
                }
                
                $user_is = User::where(['id'=>$user_id_test])->first();
                */
                $user_id = $user_is->id;
                $comment=$this->save_comment(new ShortOpinionComment(),$request->all(),$user_id);
                ShortOpinion::where(['id'=>$post->id])->update(['last_updated_at'=>Carbon::now()]);
                if($comment!=null){
                    
                    $comment->user=User::where(['id'=>$user_id])->first();
                    $cm_user = $comment->user->id;
                    $comment->likes_count=0;
                    $comment->replies_count=0;
                    $html_comment=(String)view('admin.dashboard.opinion.comments.comment')->with(['comment'=>$comment,'opinion_id'=>$request->input('opinion_id'),'liked_comments'=>[],$comment->is_active=>1]);
                    //$this->notify_followers($post,$comment,'commented');
                    return response()->json(array('status'=>'success','message'=>'comment added','comment'=>$html_comment,'total_comments'=>$total_comments));
                    $this->notify_followers($post,$comment,'commented',$cm_user);
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
            'opinion_id'=>'required',
            'comment'=>'nullable|required_without_all:comment_media,comment_image',
            'comment_media'=>'nullable|mimes:jpeg,png,gif,jpg|file|max:2050|required_without_all:comment,comment_image',
            'comment_image'=>'nullable|url|required_without_all:comment,comment_media',
        ]);

        if($validator->fails()){
            return response()->json(array('status'=>'error','message'=>'Comment can not empty'));
        }else{
            $comment=ShortOpinionComment::where(['id'=>$request->input('comment_id'),'is_active'=>1,'user_id'=>Auth::user()->id])->withCount('likes','replies')->with('user')->first();
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
                    $html_comment=(String)view('admin.dashboard.opinions.comments.comment')->with(['comment'=>$comment,'opinion_id'=>$request->input('opinion_id'),'liked_comments'=>$liked_comments]);
                    $this->notify_followers($opinion,$comment,'updated');
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

            $comment=ShortOpinionComment::where(['id'=>$request->comment_id,'short_opinion_id'=>$request->opinion_id])->first();
            if($comment){
                DB::table('notifications')
                ->where('data','like','%"event":"COMMENTED_ON_OPINION"%')
                ->where('data','like','%"comment_id":'.$comment->id.'%')
                ->delete();
                DB::table('short_opinion_comments')->where('id','=',$comment->id)->delete();
                ShortOpinionComment::where(['parent_id'=>$request->comment_id,'short_opinion_id'=>$request->opinion_id])->delete();
                ShortOpinionCommentLike::where(['comment_id'=>$comment->id])->delete();
                $replies=ShortOpinionComment::where(['parent_id'=>$request->comment_id,'short_opinion_id'=>$request->opinion_id])->get();
                foreach($replies as $reply){
                    ShortOpinionCommentLike::where('comment_id',$reply->id)->delete();
                }
                $total_comments=ShortOpinionComment::where(['short_opinion_id'=>$request->input('opinion_id'),'is_active'=>1])->count();
                return response()->json(array('status'=>'success','message'=>'comment deleted','total_comments'=>$total_comments));
            }else{
                return response()->json(array('status'=>'error','message'=>'comment not found'));
            }
        }
     }

     public function desable(Request $request){
        $validator = Validator::make($request->all(), [
            'comment_id'=>'required',
            'opinion_id'=>'required'
        ]);

        if($validator->fails()){
            return response()->json(array('status'=>'error','message'=>'Input missing'));
        }else{

            $comment=ShortOpinionComment::where(['id'=>$request->comment_id,'short_opinion_id'=>$request->opinion_id])->first();
            if($comment){
                DB::table('notifications')
                ->where('data','like','%"event":"COMMENTED_ON_OPINION"%')
                ->where('data','like','%"comment_id":'.$comment->id.'%')
                ->delete();
                $comment->status=0;
                $comment->save();
                ShortOpinionComment::where(['parent_id'=>$request->comment_id,'short_opinion_id'=>$request->opinion_id])->update(['status'=>0]);
                ShortOpinionCommentLike::where(['comment_id'=>$comment->id])->update(['is_active'=>0]);
                $replies=ShortOpinionComment::where(['parent_id'=>$request->comment_id,'short_opinion_id'=>$request->opinion_id])->get();
                foreach($replies as $reply){
                    ShortOpinionCommentLike::where('comment_id',$reply->id)->update(['is_active' => 0]);
                }
                $total_comments=ShortOpinionComment::where(['short_opinion_id'=>$request->input('opinion_id'),'is_active'=>1])->count();
                return response()->json(array('status'=>'success','message'=>'comment desabled','total_comments'=>$total_comments));
            }else{
                return response()->json(array('status'=>'error','message'=>'comment not found'));
            }
        }
     }

     public function enable(Request $request){
        $validator = Validator::make($request->all(), [
            'comment_id'=>'required',
            'opinion_id'=>'required'
        ]);

        if($validator->fails()){
            return response()->json(array('status'=>'error','message'=>'Input missing'));
        }else{

            $comment=ShortOpinionComment::where(['id'=>$request->comment_id,'short_opinion_id'=>$request->opinion_id])->first();
            if($comment){
                
                $comment->status=1;
                $comment->save();
                ShortOpinionComment::where(['parent_id'=>$request->comment_id,'short_opinion_id'=>$request->opinion_id])->update(['status'=>1]);
                ShortOpinionCommentLike::where(['comment_id'=>$comment->id])->update(['is_active'=>1]);
                $replies=ShortOpinionComment::where(['parent_id'=>$request->comment_id,'short_opinion_id'=>$request->opinion_id])->get();
                foreach($replies as $reply){
                    ShortOpinionCommentLike::where('comment_id',$reply->id)->update(['is_active' => 1]);
                }
                $total_comments=ShortOpinionComment::where(['short_opinion_id'=>$request->input('opinion_id'),'is_active'=>1])->count();
                return response()->json(array('status'=>'success','message'=>'Comment Enabled','total_comments'=>$total_comments));
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
                $admin_id =  Auth::guard('admin')->user()->id;
                if($admin_id==4){
                  $user_id_test = rand(4406,4505);
                }
                elseif($admin_id==5){
                  $user_id_test = rand(4506,4605);
                }
                elseif($admin_id==6){
                  $user_id_test = rand(4606,4705);
                }
                $user_is = User::where(['id'=>$user_id_test])->first();
                $lk_user_id= $user_is->id;
                $likeFound=ShortOpinionCommentLike::where(['user_id'=>$lk_user_id,'comment_id'=>$comment_id])->first();
                if($likeFound){
                    ShortOpinionCommentLike::where(['user_id'=>$lk_user_id,'comment_id'=>$comment_id])->delete();
                    $status='like';
                }else{
                    ShortOpinionCommentLike::create(['user_id'=>$lk_user_id,'comment_id'=>$comment_id]);
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


    protected function save_comment(ShortOpinionComment $comment,array $data, $user_id){
        if($data['parent_id']!=0){
            $found=ShortOpinionComment::where(['id'=>$data['parent_id'],'is_active'=>1])->exists();
            if(!$found){return null;}
        }
        $comment->short_opinion_id=(int)$data['opinion_id'];
        $comment->user_id=$user_id;
        $comment->parent_id=isset($data['parent_id'])?$data['parent_id']:0;
        $comment->comment=isset($data['comment'])?$data['comment']:NULL;
        $comment->media=isset($data['image'])?$data['image']:NULL;
        $comment->save();
        return $comment;
    }

    protected function notify_followers(ShortOpinion $opinion,ShortOpinionComment $comment,$event,$cm_user){
        $cmtd_user = User::where(['id'=>$user_id_test])->first();
        $followers=$cmtd_user->active_followers;
        $follower_ids=count($followers)>0?$followers->pluck('id')->toArray():[];

        if($opinion->user && $opinion->user->id!==$cmtd_user->id && !in_array($opinion->user->id,$follower_ids)){
            array_push($follower_ids,$opinion->user->id);
            $followers->push($opinion->user);
        }
        $fcm_tokens=UserDevice::select('gcm_token')->distinct()->whereIn('user_id',$follower_ids)->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
        try{
            foreach(array_chunk($fcm_tokens,100) as $chunk) {
                dispatch(new CommentedOnShortOpinionJob($opinion,$comment,$cmtd_user,$chunk));
            }
            foreach($followers as $follower){
                Notification::send($follower,new CommentedOnShortOpinion($opinion,$comment,$cmtd_user,$fcm_tokens));
            }
        }catch(\Exception $e){}
    }

}
