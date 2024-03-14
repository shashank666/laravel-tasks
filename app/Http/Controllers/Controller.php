<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Model\FileManager;
use App\Model\Polls;
use App\Model\Post;
use App\Model\ShortOpinion;
use App\Model\User;
use App\Model\UserDevice;
use App\Model\PollComment;
use App\Model\Community;

use App\Model\CommunityMember;


use DB;
use Carbon\Carbon;
use Config;
use Nexmo\Laravel\Facade\Nexmo;
use Embed\Embed;
use Embed\Providers\OEmbed\Poll;
use Image;
use ImageOptimizer;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // function to replace null value with "" in given object
    public function remove_null($parameter)
    {
        array_walk_recursive($parameter, function (&$item, $key) {
            $item = null === $item ? '' : $item;
        });
    }

    // function for save uploaded file entry in file manager table
    public function save_file_to_db($uniqueid,$path,$name,$original_name,$event,$size,$extension,$user_id){
        $file = new FileManager();
        $file->unique_id=$uniqueid;
        $file->path=$path;
        $file->name=$name;
        $file->original_name=$original_name;
        $file->event=$event;
        $file->size=$size;
        $file->extension=$extension;
        $file->user_id=$user_id;
        $file->save();
        return $file;
    }

     // function for get pagination meta
    public function get_meta($model){
        $meta= [
           'total'=>$model->total(),
           'perPage'=>$model->perPage(),
           'currentPage'=>$model->currentPage(),
           'count'=>$model->count(),
           'hasMorePages'=>$model->hasMorePages(),
           'lastPage'=>$model->lastPage(),
           'nextPageUrl'=>$model->nextPageUrl()!=null?$model->nextPageUrl():"",
           'previousPageUrl'=>$model->previousPageUrl()!=null?$model->previousPageUrl():"",
           'nextPage'=>$model->nextPageUrl()!=null?(int)explode('=',$model->nextPageUrl())[1]:0,
           'prevPage'=>$model->previousPageUrl()!=null?(int)explode('=',$model->previousPageUrl())[1]:0,
        ];
     return $meta;
    }

     // function for format posts pagination
     public function format_posts($posts,$liked_ids,$bookmarked_ids){
        $formatted=$posts->getCollection()->transform(function($post,$key) use($liked_ids,$bookmarked_ids){
            return $this->formatted_post($post,$liked_ids,$bookmarked_ids);
        });
        return $formatted;
    }

    public function format_api_posts($posts,$liked_ids,$bookmarked_ids){
        $formatted=$posts->getCollection()->transform(function($post,$key) use($liked_ids,$bookmarked_ids){
            return $this->formatted_test_post($post,$liked_ids,$bookmarked_ids);
        });
        return $formatted;
    }

    public function formatted_polls(Polls $poll, $user_id, $poll_votes){
        $formatted_date=Carbon::parse($poll->created_at)->diffInHours(Carbon::now(), false) >= 24?(Carbon::parse($poll->created_at)->format("j M Y, H:i A")):(Carbon::parse($poll->created_at)->diffForHumans());
        $formatted_enddate=null;
        if($poll->end_date!=null){
            if(Carbon::now()->gt($poll->end_date)){
                if($poll->visibility==1){
                Polls::where(['id'=>$poll->id])->update(['visibility'=>0]);
                }
            }
        $formatted_enddate=Carbon::parse($poll->end_date)->diffInHours(Carbon::now(), false) >= 24?(Carbon::parse($poll->end_date)->format("j M Y, H:i A")):(Carbon::parse($poll->end_date)->diffForHumans());
        }else{
            $formatted_enddate=$poll->end_date;
        }

        
        try{
        $profile_user=User::where(['id'=>$poll->user_id,'is_active'=>1])->first();
        }catch(\Exception $e){
        $profile_user=null;
        }
       
        $comments_count=PollComment::where(['poll_id'=>$poll->id])->count();

        $custom_poll=[
            'id' => $poll->id,
            'title' => $poll->title,
            'slug' =>$poll->slug,
            'user_id'=>$poll->user_id,
            'description'=>$poll->description,
            'poll_type'=>$poll->poll_type,
            'polltype_id'=>$poll->polltype_id,
            'visibility'=>$poll->visibility,
            'user'=>$profile_user,
            'started_at'=>$poll->started_at,
            'paused_at'=>$poll->paused_at,
            'created_at'=>$formatted_date,
            'poll_votes'=>$poll_votes,
            'poll_end_date'=>$formatted_enddate,
            'commentsCount'=>$comments_count,
            'updated_at'=>$poll->updated_at

        ];

        return $custom_poll;
    }

    public function formatted_community(Community $community){
        // $formatted_date=Carbon::parse($poll->created_at)->diffInHours(Carbon::now(), false) >= 24?(Carbon::parse($poll->created_at)->format("j M Y, H:i A")):(Carbon::parse($poll->created_at)->diffForHumans());
        // $formatted_enddate=null;
        
        
        try{
        $profile_user=User::where(['id'=>$community->user_id,'is_active'=>1])->first();
        }catch(\Exception $e){
        $profile_user=null;
        }
       
        $members_count=CommunityMember::where(['community_id'=>$community->id,'is_active'=>1])->count();

        $community_category = DB::table('categories')->where(['id'=>$community->contest_category])->first();

        $custom_poll=[
            'id' => $community->id,
            'name' => $community->name,
            'uuid' => $community->uuid,
            'image' => $community->image,
            'cover_image' =>$community->cover_image,
            'user_id'=>$community->user_id,
            'contest_category'=>$community->contest_category,
            'description'=>$community->description,
            'user'=>$profile_user,
            'created_at'=>$community->created_at,
            'updated_at'=>$community->updated_at,
            'members_count' => $members_count,
            'category' => $community_category

        ];

        return $custom_poll;
    }

    // function to format post object
    public function formatted_post(Post $post,$liked_ids,$bookmarked_ids){
        $this->remove_null($post);
        $this->remove_null($post->user);
        $is_liked=in_array($post->id,$liked_ids)?1:0;
        $is_bookmarked=in_array($post->id,$bookmarked_ids)?1:0;
        $body='<div align="justify">'.$post->body.'</div>';
        $coverimage=$this->get_resized_url($post->coverimage,'350x250');

        $custom_post= [
            'id' => $post->id,
            'title' => $post->title,
            'slug'=> $post->slug,
            'uuid'=>$post->uuid,
            'cover_type'=>$post->cover_type,
            'coverimage'=>$coverimage,
            'body'=>$body,
            'plainbody'=>$post->plainbody,
            'readtime'=>$post->readtime,
            'status'=>$post->status,
            'is_active'=>$post->is_active,
            'user_id'=>$post->user_id,
            'views'=>$post->views,
            'likes'=>$post->likes,
            'likes_count'=>$post->likes_count==null?0:$post->likes_count,
            'comments_count'=>$post->comments_count==null?0:$post->comments_count,
            'plagiarism_checked'=>$post->plagiarism_checked,
            'platform'=>$post->platform,
            'is_plagiarized'=>$post->is_plagiarized,
            'plagiarism_percentage'=>$post->plagiarism_percentage,
            'platform'=>$post->platform,
            'is_liked'=>$is_liked,
            'is_bookmarked'=>$is_bookmarked,
            'created_at'=>$post->created_at,
            'updated_at'=>$post->updated_at,
            'categories'=>$post->categories,
            'threads'=>$post->threads,
            'keywords'=>count($post->keywords)>0 ? $post->keywords->pluck('name')->toArray():[],
            'user'=>$post->user,
            'share_urls'=>$post->share_urls
        ];
        return $custom_post;
    }

    // function to format post object
    public function formatted_test_post(Post $post,$liked_ids,$bookmarked_ids){
        $this->remove_null($post);
        $this->remove_null($post->user);
        $is_liked=in_array($post->id,$liked_ids)?1:0;
        $is_bookmarked=in_array($post->id,$bookmarked_ids)?1:0;
        $body='<div align="justify">'.$post->body.'</div>';
        $coverimage=$this->get_resized_url($post->coverimage,'350x250');

        $custom_post= [
            'id' => $post->id,
            'title' => $post->title,
            'slug'=> $post->slug,
            'uuid'=>$post->uuid,
            'cover_type'=>$post->cover_type,
            'coverimage'=>$coverimage,
            'body'=>$body,
            'plainbody'=>$post->plainbody,
            'readtime'=>$post->readtime,
            'status'=>$post->status,
            'is_active'=>$post->is_active,
            'user_id'=>$post->user_id,
            'views'=>$post->views,
            'likes'=>$post->likes,
            'likes_count'=>$post->likes_count==null?0:$post->likes_count,
            'comments_count'=>$post->comments_count==null?0:$post->comments_count,
            'plagiarism_checked'=>$post->plagiarism_checked,
            'platform'=>$post->platform,
            'is_plagiarized'=>$post->is_plagiarized,
            'plagiarism_percentage'=>$post->plagiarism_percentage,
            'platform'=>$post->platform,
            'is_liked'=>$is_liked,
            'is_bookmarked'=>$is_bookmarked,
            'created_at'=>$post->created_at,
            'updated_at'=>array('date'=>Carbon::now(),'timezone_type'=>3,'timezone'=>'Asia/Kolkata'),
            'categories'=>$post->categories,
            'threads'=>$post->threads,
            'keywords'=>count($post->keywords)>0 ? $post->keywords->pluck('name')->toArray():[],
            'user'=>$post->user,
            'share_urls'=>$post->share_urls
        ];
        return $custom_post;
    }


    //function to set Is_agree and is_disagree values
    public function formatted_opinion_AD(ShortOpinion $opinion,$liked_ids, $Agree_ids, $Disagree_ids,$myagreedIds,$mydisagreedIds){
        $formatted_date=Carbon::parse($opinion->created_at)->diffInHours(Carbon::now(), false) >= 24?(Carbon::parse($opinion->created_at)->format("j M Y, H:i A")):(Carbon::parse($opinion->created_at)->diffForHumans());
        $opinion->cover=$opinion->cover==null?[]:explode(",",$opinion->cover);
        $opinion->likes=$opinion->likesCount==null?0:$opinion->likesCount;
		//new changes for agree disagree counts
		$opinion->agree=$opinion->agreeCount==null?0:$opinion->agreeCount;
		$opinion->disagree=$opinion->disagreeCount==null?0:$opinion->disagreeCount;
		
        $opinion->comments=$opinion->commentsCount==null?0:$opinion->commentsCount;
        $opinion->is_liked=in_array($opinion->id,$liked_ids)?1:0;
		$opinion->is_agreed=in_array($opinion->id,$myagreedIds); //$MyAgree_ids
		$opinion->is_disagreed=in_array($opinion->id,$mydisagreedIds); //$MyDisAgree_ids
        $opinion->formatted_date=$formatted_date;
        unset($opinion->likesCount);
        unset($opinion->comments_count);
        unset($opinion->likes_count);
        unset($opinion->comments_count);
 unset($opinion->agree_Count);
 unset($opinion->disagree_Count);
//unset($opinion->is_agreed);
        $opinion->links=$opinion->links!==null?json_decode($opinion->links,true):[];
        $this->remove_null($opinion);
        $user['id']=$opinion->user->id;
        $user['name']=$opinion->user->name;
        $user['username']=$opinion->user->username;
        $user['unique_id']=$opinion->user->unique_id;
        $user['image']=$opinion->user->image;
        $user['profile_url']=$opinion->user->profile_url;
        unset($opinion->user);
        $opinion->user=$user;
        $this->remove_null($opinion->user);
        if($opinion->latest_comments){
            $opinion->latest_comments->map(function($comment){
                 $comment->media=$comment->media==null?"":$comment->media;
                 $comment->comment=$comment->comment==null?"":$comment->comment;
            });
         }
        $custom=$opinion;
        return $custom;
    }
    
         // function to format opinion object
     public function formatted_opinion(ShortOpinion $opinion,$liked_ids,$disagreed_ids){
            $formatted_date=Carbon::parse($opinion->created_at)->diffInHours(Carbon::now(), false) >= 24?(Carbon::parse($opinion->created_at)->format("j M Y, H:i A")):(Carbon::parse($opinion->created_at)->diffForHumans());
            $opinion->cover=$opinion->cover==null?[]:explode(",",$opinion->cover);
            $opinion->likes=$opinion->likesCount==null?0:$opinion->likesCount;
            $opinion->disagree=$opinion->disagreeCount==null?0:$opinion->disagreeCount;
            //new changes for agree disagree counts
            $opinion->agree=$opinion->agreeCount==null?0:$opinion->agreeCount;
            $opinion->disagree=$opinion->disagreeCount==null?0:$opinion->disagreeCount;
            
            $opinion->comments=$opinion->commentsCount==null?0:$opinion->commentsCount;
            $opinion->is_liked=in_array($opinion->id,$liked_ids)?1:0;
            $opinion->is_disagreed=in_array($opinion->id,$disagreed_ids)?1:0;
            $opinion->formatted_date=$formatted_date;
            $opinion->community_id=$opinion->community_id==null?0:$opinion->community_id;
            unset($opinion->likesCount);
            unset($opinion->disagreeCount);
            unset($opinion->commentsCount);
            unset($opinion->likes_count);
            unset($opinion->disagreeCount);
            unset($opinion->comments_count);
     unset($opinion->agree_Count);
     unset($opinion->disagree_Count);
    
            $opinion->links=$opinion->links!==null?json_decode($opinion->links,true):[];
            $this->remove_null($opinion);
            $user['id']=$opinion->user->id;
            $user['name']=$opinion->user->name;
            $user['username']=$opinion->user->username;
            $user['unique_id']=$opinion->user->unique_id;
            $user['image']=$opinion->user->image;
            $user['profile_url']=$opinion->user->profile_url;
            unset($opinion->user);
            $opinion->user=$user;
            $this->remove_null($opinion->user);
            if($opinion->latest_comments){
                $opinion->latest_comments->map(function($comment){
                     $comment->media=$comment->media==null?"":$comment->media;
                     $comment->comment=$comment->comment==null?"":$comment->comment;
                });
             }
            $custom=$opinion;
            return $custom;
        }

    // new function to format opinion object
public function formatted_opinion_new(ShortOpinion $opinion,$liked_ids){
       $formatted_date=Carbon::parse($opinion->created_at)->diffInHours(Carbon::now(), false) >= 24?(Carbon::parse($opinion->created_at)->format("j M Y, H:i A")):(Carbon::parse($opinion->created_at)->diffForHumans());
       $opinion->cover=$opinion->cover==null?[]:explode(",",$opinion->cover);
       $opinion->likes=$opinion->likesCount==null?0:$opinion->likesCount;
       //new changes for agree disagree counts
       $opinion->agree=$opinion->agreeCount==null?0:$opinion->agreeCount;
       $opinion->disagree=$opinion->disagreeCount==null?0:$opinion->disagreeCount;
       
       $opinion->comments=$opinion->commentsCount==null?0:$opinion->commentsCount;
       $opinion->is_liked=in_array($opinion->id,$liked_ids)?1:0;
       $opinion->formatted_date=$formatted_date;
       unset($opinion->likesCount);
       unset($opinion->commentsCount);
       unset($opinion->likes_count);
       unset($opinion->comments_count);
unset($opinion->agree_Count);
unset($opinion->disagree_Count);

       $opinion->links=$opinion->links!==null?json_decode($opinion->links,true):[];
       $this->remove_null($opinion);
       $user['id']=$opinion->user->id;
       $user['name']=$opinion->user->name;
       $user['username']=$opinion->user->username;
       $user['unique_id']=$opinion->user->unique_id;
       $user['image']=$opinion->user->image;
       $user['profile_url']=$opinion->user->profile_url;
       unset($opinion->user);
       $opinion->user=$user;
       $this->remove_null($opinion->user);
       if($opinion->latest_comments){
           $opinion->latest_comments->map(function($comment){
                $comment->media=$comment->media==null?"":$comment->media;
                $comment->comment=$comment->comment==null?"":$comment->comment;
           });
        }
       $custom=$opinion;
       return $custom;
   }




    public function formatted_comment($comment,$liked_ids,$disagreed_ids){
        $comment->is_liked=in_array($comment->id,$liked_ids)?1:0;
        $comment->is_disagreed=in_array($comment->id,$disagreed_ids)?1:0;
        $this->remove_null($comment);
        return $comment;
    }

    // function to format user object
    public function formatted_user($following_ids,User $user){
        $this->remove_null($user);
        $user->is_followed=in_array($user->id,$following_ids)?1:0;
        return $user;
    }

    // function for format follower pagination
    public function format_follower($followers){
        $formatted_followers=$followers->getCollection()->transform(function($follower,$key){
            $this->remove_null($follower);
           $user= isset($follower->leader) && !empty($follower->leader)?$follower['leader']:$follower['follower'];
           $this->remove_null($user);
             $custom_user= [
                'id' => $follower->id,
                'follower_id'=> $follower->follower_id,
                'leader_id'=> $follower->leader_id,
                'is_active'=> $follower->is_active,
                'created_at'=>Carbon::parse($follower->created_at)->toDateTimeString(),
                'updated_at'=>Carbon::parse($follower->updated_at)->toDateTimeString(),
                'user'=>$user
            ];
            return $custom_user;
        });
        return $formatted_followers;
    }

    // function for format notification pagination
    public function format_notifications($notifications){
        $formatted=$notifications->getCollection()->transform(function($notification,$key){
            $this->remove_null($notification);
            //$notification->data=$notification->data!=null?json_decode($notification->data,true):'';
            return $notification;
        });
        return $formatted;
    }

    // function for get loggedin users followed category ids
    public function my_followed_categoryids($user_id){
        $followed_categoryids=[];
        $followed_categoryids=DB::table('categories')
        ->join('category_followers', 'categories.id', '=', 'category_followers.category_id')
        ->where(['category_followers.user_id'=>$user_id,'category_followers.is_active'=>1,'categories.is_active'=>1])
        ->select('categories.id')->get()->pluck('id')->toArray();
        return $followed_categoryids;
    }

    // function for get loggedin users followed thread ids
    public function my_followed_threadids($user_id){
        $followed_threadids=[];
        $followed_threadids=DB::table('threads')
        ->join('thread_followers', 'threads.id', '=', 'thread_followers.thread_id')
        ->where(['thread_followers.user_id'=>$user_id,'thread_followers.is_active'=>1,'threads.is_active'=>1])
        ->select('threads.id')->get()->pluck('id')->toArray();
        return $followed_threadids;
    }

    // function for get loggedin users liked thread ids
    public function my_liked_threadids($user_id){
        $liked_threadids=[];
        $liked_threadids=DB::table('threads')
        ->join('thread_likes', 'threads.id', '=', 'thread_likes.thread_id')
        ->where(['thread_likes.user_id'=>$user_id,'thread_likes.is_active'=>1,'threads.is_active'=>1])
        ->select('threads.id')->get()->pluck('id')->toArray();
        return $liked_threadids;
    }

    // function for get loggedin users bookmarked post ids
    public function my_bookmarked_postids($user_id){
        $bookmarked_postsids=[];
        $bookmarked_postsids = DB::table('posts')
        ->join('bookmarks', 'posts.id', '=', 'bookmarks.post_id')
        ->where(['bookmarks.user_id'=>$user_id,'bookmarks.is_active'=>1,'posts.is_active'=>1])
        ->select('posts.id')->get()->pluck('id')->toArray();
        return $bookmarked_postsids;
    }

    // function for get loggedin users liked post ids
    public function my_liked_postids($user_id){
        $liked_postsids=[];
        $liked_postsids = DB::table('posts')
        ->join('likes', 'posts.id', '=', 'likes.post_id')
        ->where(['likes.user_id'=>$user_id,'likes.is_active'=>1,'posts.is_active'=>1])
        ->select('posts.id')->get()->pluck('id')->toArray();
        return $liked_postsids;
    }

    // function for get loggedin users liked opinion ids
    public function my_liked_opinionids($user_id){
        $liked_opinionids=[];
        $liked_opinionids = DB::table('short_opinions')
        ->join('short_opinion_likes', 'short_opinions.id', '=', 'short_opinion_likes.short_opinion_id')
        ->where(['short_opinion_likes.user_id'=>$user_id,'short_opinion_likes.is_active'=>1,'short_opinions.is_active'=>1])
        ->select('short_opinions.id')->get()->pluck('id')->toArray();
        return $liked_opinionids;
    }

      // function for get loggedin users liked post comments
    public function my_liked_commentids($user_id){
        $liked_commentids=[];
        $liked_commentids = DB::table('comments')
        ->join('comments_likes', 'comments.id', '=', 'comments_likes.comment_id')
        ->where(['comments_likes.user_id'=>$user_id,'comments_likes.is_active'=>1,'comments.is_active'=>1])
        ->select('comments.id')->get()->pluck('id')->toArray();
        return $liked_commentids;
    }

    // function for get loggedin users liked opinion comments
    public function my_liked_opinion_commentids($user_id){
        $liked_commentids=[];
        $liked_commentids = DB::table('short_opinion_comments')
        ->join('short_opinion_comments_likes', 'short_opinion_comments.id', '=', 'short_opinion_comments_likes.comment_id')
        ->where(['short_opinion_comments_likes.user_id'=>$user_id,'short_opinion_comments_likes.is_active'=>1,'short_opinion_comments.is_active'=>1])
        ->select('short_opinion_comments.id')->get()->pluck('id')->toArray();
        return $liked_commentids;
    }
    // function for get loggedin users disagreed opinion comments
    public function my_disagreed_opinion_commentids($user_id){
        $disagreed_commentids=[];
        $disagreed_commentids = DB::table('short_opinion_comments')
        ->join('short_opinion_comments_disagree', 'short_opinion_comments.id', '=', 'short_opinion_comments_disagree.comment_id')
        ->where(['short_opinion_comments_disagree.user_id'=>$user_id,'short_opinion_comments_disagree.is_active'=>1,'short_opinion_comments.is_active'=>1])
        ->select('short_opinion_comments.id')->get()->pluck('id')->toArray();
        return $disagreed_commentids;
    }

    public function my_liked_poll_commentids($user_id){
        $liked_commentids=[];
        $liked_commentids = DB::table('poll_comments')
        ->join('poll_comments_likes', 'poll_comments.id', '=', 'poll_comments_likes.comment_id')
        ->where(['poll_comments_likes.user_id'=>$user_id,'poll_comments_likes.is_active'=>1,'poll_comments.is_active'=>1])
        ->select('poll_comments.id')->get()->pluck('id')->toArray();
        return $liked_commentids;
    }
    // function for get loggedin users disagreed opinion comments
    public function my_disagreed_poll_commentids($user_id){
        $disagreed_commentids=[];
        $disagreed_commentids = DB::table('poll_comments')
        ->join('poll_comments_disagree', 'poll_comments.id', '=', 'poll_comments_disagree.comment_id')
        ->where(['poll_comments_disagree.user_id'=>$user_id,'poll_comments_disagree.is_active'=>1,'poll_comments.is_active'=>1])
        ->select('poll_comments.id')->get()->pluck('id')->toArray();
        return $disagreed_commentids;
    }


    // custom function for get user_id from api_token
    public function get_user_from_api_token($header){
        $api_token=substr($header,7);
        $device=UserDevice::where(['api_token'=>$api_token,'is_active'=>1])->first();
        if($device){
            return $device->user_id;
        }else{
            return -1;
        }
    }
    public function get_user_name_from_api_token($header){
        $api_token=substr($header,7);
        $device=UserDevice::where(['api_token'=>$api_token,'is_active'=>1])->first();
        $user=User::where(['id'=>$device->user_id,'is_active'=>1])->first();
        if($device){
            return $user->name;
        }else{
            return -1;
        }
    }

    // function for send otp using txtlocal
    public function send_otp($OTP,$OTP_EXPIRED,$mobile){
        $EXPIRED=Carbon::parse($OTP_EXPIRED)->format('H:i:s');
        $msg1 = "Thank you for registering with Opined . Your OTP is ";
        $msg2 = $OTP." valid till ".$EXPIRED;
        $msg3=".Do not share OTP for security reasons.";
        $message =  rawurlencode($msg1.$msg2.$msg3);
        $test = "0";
        $sender = urlencode("Opined");

         $data = array('apikey' =>Config::get('app.company')->sms_apikey,
        'numbers' => $mobile,
        'username'=>Config::get('app.company')->sms_username,
        'password'=>Config::get('app.company')->sms_password,
        'sender' => $sender,
        'message' => $message);

        $ch = curl_init(Config::get('app.company')->sms_apiurl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    // function for send Security key using nexemo
    public function nexemo_send_key($KEY,$KEY_EXPIRED,$mobile){
        $EXPIRED=Carbon::parse($KEY_EXPIRED)->format('H:i:s');
        $to = preg_replace('/\D+/','',$mobile);
        $sms='Use '.$KEY.' as OTP to verify your number and complete sign-up on Opined. OTP is valid till '.$KEY_EXPIRED.' Do not share OTP for security reasons. - OPINED';
        $message=Nexmo::message()->send([
            'to'   => $to,
            'from' => 'OPINED',
            'text' => $sms
        ]);
        return $message->getResponseData();
    }

    // function for send otp using nexemo
    public function nexemo_send_otp($OTP,$OTP_EXPIRED,$mobile){
        $EXPIRED=Carbon::parse($OTP_EXPIRED)->format('H:i:s');
        $to = preg_replace('/\D+/','',$mobile);
        $sms='Use '.$OTP.' as OTP to verify your number and complete sign-up on Opined. OTP is valid till '.$OTP_EXPIRED.' Do not share OTP for security reasons. - OPINED';
       // $sms='Use '.$OTP.' as OTP to verify your number and complete sign-up on Opined. Do not share OTP for security reasons.';
        $message=Nexmo::message()->send([
            'to'   => $to,
            'from' => 'OPINED',
            'text' => $sms
        ]);
        return $message->getResponseData();
    }

    // function for send otp to us/canada using nexemo
    public function nexemo_send_otp_to_USA($OTP,$OTP_EXPIRED,$mobile){
        $EXPIRED=Carbon::parse($OTP_EXPIRED)->format('H:i:s');
        $to = preg_replace('/\D+/','',$mobile);
        $sms='Use '.$OTP.' as OTP to verify your number and complete sign-up on Opined. OTP is valid till '.$OTP_EXPIRED.' Do not share OTP for security reasons. - OPINED';
        /*$message=Nexmo::message()->send([
            'to'   => $to,
            'from' => '31061',
            'text' => $sms
        ]);*/
        $url = 'https://rest.nexmo.com/sc/us/2fa/json?' . http_build_query([
            'api_key' => '31b3a44f',
            'api_secret' => 'Opined@Nexmo$$$$1234',
            'to' => $to,
            'pin' => $OTP
            ]);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        //return $message->getResponseData();
    }

    public function filesize_formatted($size)
    {
    $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }

    public function optimize_image($extension,$folder,$name,$original_size){
        if(in_array(strtolower($extension),["jpg","jpeg","png","svg","gif"])){
            try{
                ImageOptimizer::optimize(storage_path('app/public/'.$folder.'/'.$name));
                $size = Storage::size('public/'.$folder.'/'.$name);
                return $size;
            }catch(\Exception $e){
                return $original_size;
            }
        }
    }

    // function for get resized_url by checking image exists in storage or not
    public function get_resized_url($original_url,$WH){
        $folder=basename(pathinfo($original_url, PATHINFO_DIRNAME));
        $filename=pathinfo($original_url, PATHINFO_FILENAME);
        $extension= pathinfo($original_url, PATHINFO_EXTENSION);
        $resize=$folder.'/'.$filename.'_'.$WH.'.'.$extension;
        if(Storage::exists('public/'.$resize)){
           return url('/storage/'.$resize);
        }else{
            return $original_url;
        }
    }

    public function generate_resized_image($sourcePath,$destinationPath,$sizes){
        foreach($sizes as $size){
            $filename=pathinfo($sourcePath, PATHINFO_FILENAME);
            $extension= pathinfo($sourcePath, PATHINFO_EXTENSION);
            $imagename = $filename.'_'.$size[0].'x'.$size[1].'.'.$extension;
            Image::make($sourcePath)->resize($size[0],$size[1])->save($destinationPath.'/'.$imagename);
        }
    }

    // function for get video duration and check it is more than 3 min or not
    public function check_video_duration_exceeded($filePath){
        try{
            $getID3 = new \getID3;
            $file = $getID3->analyze($filePath);
            $duration =  (int)$file['playtime_seconds'];
            return $duration>182?true:false;
        }catch(\Exception $e){
            return false;
        }
    }

    // function for create slug from post title
    public function create_slug($title){
        $slug =  preg_match("/[a-z]/i", $title)?str::slug($title,'-'):str_replace(' ', '-',$title);
        if(strlen($slug)==0){ $slug=Carbon::now()->format('Ymd').'-'.uniqid(); }
        $posts  = Post::whereRaw("slug REGEXP '^{$slug}([0-9]*)?$'")->get();
        $count = count($posts) + 1;
        return ($count > 1) ? $slug.$count : $slug;
    }

    // function for fetch information from given url
    public function fetch_data_from_url($url){
        try{
            $info = Embed::create($url,[
                'min_image_width' => 100,
                'min_image_height' => 100,
                'follow_canonical' => true,
                'html' => [
                    'max_images' => 2,
                    'external_images' => false
                ]
            ]);
            if($info){

                $information['status']='OK';
                $information['title']=$info->title;
                $information['description']=$info->description;
                $information['url']=$info->url;
                $information['type']=$info->type;
                $information['link']['tags']=$info->tags;
                //$information['images']=$info->images;
                $information['image']=$info->image;
                $information['imageWidth']=$info->imageWidth;
                $information['imageHeight']=$info->imageHeight;

                $information['code']=$info->code;
                $information['width']=$info->width;
                $information['height']=$info->height;
                $information['aspectRatio']=$info->aspectRatio;

                $information['authorName']=$info->authorName;
                $information['authorUrl']=$info->authorUrl;
                $information['providerName']=$info->providerName;
                $information['providerUrl']=$info->providerUrl;
                //$information['providerIcons']=$info->providerIcons;
                $information['providerIcon']=$info->providerIcon;
                $information['publishedDate']=$info->publishedDate;
                return $information;
            }else{
                $information['status']='error';
                $information['url']=$url;
                return $information;
            }
        }catch(\Exception $e){
            $information['status']='error';
            $information['url']=$url;
            return $information;
        }
    }

    // function to format android os version
    public function format_android_version($version){
        if($version!=null){
            $arr=explode('.',$version);
            if(count($arr)==1){return $version.'.0.0';}
            if(count($arr)==2){return $version.'.0';}
            else{return $version;}
        }else{
            return $version;
        }
    }

    //function for get android os name by version
    public function get_os_version_name($version){
         if($version=='2.0.0' || $version=='2.1.0' || $version=='2.6.29'){
            return 'Eclair';
         }else if($version=='2.2.0' || $version=='2.2.3' || $version=='2.6.32'){
             return 'Froyo';
         }else if($version=='2.3.0' || $version=='2.3.7'|| $version=='2.6.35'){
             return 'Gingerbread';
         }else if($version=='3.0.0'|| $version=='3.2.6'){
             return 'Honeycomb';
         }else if($version=='4.0.0' || $version=='4.0.4'){
             return 'Ice Cream Sandwich';
         }else if($version=='4.1.0' || $version=='4.3.1'){
             return 'Jelly Bean';
         }else if($version=='4.4.0' || $version=='4.4.4'){
             return 'KitKat';
         }else if($version=='5.0.0' || $version=='5.1.1'){
             return 'Lollipop';
         }else if($version=='6.0.0' || $version=='6.0.1'){
             return 'Marshmallow';
         }else if($version=='7.0.0' || $version=='7.1.1' || $version=='7.1.2'){
             return 'Nougat';
         }else if($version=='8.0.0' || $version=='8.1.0'){
             return 'Oreo';
         }else if($version=='9.0.0'){
             return 'Pie';
         }else{
             return 'Android';
         }
    }

}
