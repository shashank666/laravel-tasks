<?php

namespace App\Http\Controllers\Frontend\User;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Notifications\Frontend\PostCreated;
use App\Notifications\Frontend\PostLiked;
use App\Notifications\Frontend\CommentedOnPost;
use App\Notifications\Frontend\ThreadLiked;
use App\Notifications\Frontend\UserFollowed;
use App\Notifications\Frontend\CommentedOnShortOpinion;
use App\Notifications\Frontend\ShortOpinionCreated;
use App\Notifications\Frontend\ShortOpinionLiked;

use App\Jobs\AndroidPush\PostCreatedJob;
use App\Jobs\AndroidPush\PostLikedJob;
use App\Jobs\AndroidPush\CommentedOnPostJob;
use App\Jobs\AndroidPush\ThreadLikedJob;
use App\Jobs\AndroidPush\UserFollowedJob;
use App\Jobs\AndroidPush\CommentedOnShortOpinionJob;
use App\Jobs\AndroidPush\ShortOpinionCreatedJob;
use App\Jobs\AndroidPush\ShortOpinionLikedJob;


use App\Model\User;
use App\Model\UserDevice;
use App\Model\Post;
use App\Model\Comment;
use App\Model\Thread;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionComment;
use Notification;

use Auth;

class WebPushController extends Controller
{
      public function __construct(){
        $this->middleware('auth');
      }

      public function webpush(){
        return view('tester.notification.webpush');
     }


     public function testing(Request $request,$number){
        $users=User::where('id',Auth::user()->id)->get();
        $post=Post::where('id',16)->first();
        $comment=Comment::where('user_id',Auth::user()->id)->first();
        $thread=Thread::where('id',6)->first();
        $user=User::find(33);
        $opinion=ShortOpinion::where('user_id',Auth::user()->id)->first();
        $opinion_comment=ShortOpinionComment::where('user_id',Auth::user()->id)->first();
        $fcm_tokens=UserDevice::where('user_id',Auth::user()->id)->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();

        if($number==1){
            dispatch(new PostCreatedJob($post,Auth::user(),$fcm_tokens));
            //Notification::send($users,new PostCreated($post,Auth::user(),$fcm_tokens));
        }else if($number==2){
            dispatch(new PostLikedJob($post,Auth::user(),$fcm_tokens));
            //Notification::send($users,new PostLiked($post,Auth::user(),$fcm_tokens));
        }else if($number==3){
            dispatch(new CommentedOnPostJob($post,$comment,Auth::user(),$fcm_tokens));
            //Notification::send($users,new CommentedOnPost($post,$comment,Auth::user(),$fcm_tokens));
        }else if($number==4){
            dispatch(new ThreadLikedJob($thread,Auth::user(),$fcm_tokens));
            //Notification::send($users,new ThreadLiked($thread,Auth::user(),$fcm_tokens));
        }else if($number==5){
            dispatch(new UserFollowedJob($user,$fcm_tokens));
            //Notification::send($users,new UserFollowed($user,$fcm_tokens));
        }else if($number==6){
            dispatch(new ShortOpinionCreatedJob($opinion,Auth::user(),$fcm_tokens));
            //Notification::send($users,new ShortOpinionCreated($opinion,Auth::user(),$fcm_tokens));
        }else if($number==7){
            dispatch(new ShortOpinionLikedJob($opinion,Auth::user(),$fcm_tokens));
            //Notification::send($users,new ShortOpinionLiked($opinion,Auth::user(),$fcm_tokens));
        }else{
            dispatch(new CommentedOnShortOpinionJob($opinion,$opinion_comment,Auth::user(),$fcm_tokens));
            //Notification::send($users,new CommentedOnShortOpinion($opinion,$opinion_comment,Auth::user(),$fcm_tokens));
        }
        return "done";
     }

      public function subscribe(Request $request){
          $this->validate($request,[
              'endpoint'    => 'required',
              'keys.auth'   => 'required',
              'keys.p256dh' => 'required'
          ]);
          $endpoint = $request->endpoint;
          $token = $request->keys['auth'];
          $key = $request->keys['p256dh'];
          $user = Auth::user();
          $user->updatePushSubscription($endpoint, $key, $token);

          return response()->json(['success' => true],200);
      }

      public function unsubscribe(Request $request){
        $this->validate($request,[
            'endpoint'    => 'required'
        ]);
        $user = Auth::user();
        $user->deletePushSubscription($request->endpoint);
        return response()->json(['success' => true],200);
      }


}
