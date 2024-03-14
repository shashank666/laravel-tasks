<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DB;
use Mail;
use App\Mail\Auth\ResetPasswordMail;
use NotificationChannels\WebPush\HasPushSubscriptions;
use App\Model\Point;
use App\Model\Polls;
use App\Model\UserAchievement;
use App\Model\BlockedUser;


class User extends Authenticatable
{
    use Notifiable;
    use HasPushSubscriptions;
    protected $table='users';
    public $primaryKey='id';
    protected $appends = ['profile_url'];

    protected $fillable = [
        'name','username','unique_id','email','phone_code','mobile',
         'password','reset_token','reset_token_expired_at','image','cover_image','provider', 'provider_id','email_verified','mobile_verified','verify_token','mobile_otp','mobile_otp_expired_at','otp_attempts','last_attempts',
         'is_active','is_subscribed','keywords','bio','gender','birthdate',
         'facebook_url','twitter_url','linkedin_url','instagram_url','youtube_channel_url','website_url'
         ,'views','platform','contacts_saved','contacts_saved_at','last_login_at','last_login_ip'
    ];

    protected $hidden = [
      'password', 'remember_token',
    ];

    public function locations(){
      return $this->hasMany('App\Model\UserLocation', 'user_id', 'id');
    }

    public function devices(){
		  return $this->hasMany('App\Model\UserDevice', 'user_id', 'id');
	  }

    public function getProfileUrlAttribute(){
      return url($this->username);
    }

    public function account_details(){
      return  $this->hasOne('App\Model\UserAccount','user_id','id');
    }

      public function posts(){
        return $this->hasMany('App\Model\Post');
      }

      public function short_opinions(){
        return $this->hasMany('App\Model\ShortOpinion');
      }

      public function bookmarks(){
          return $this->belongsToMany('App\Model\Post','bookmarks','user_id','post_id')->where('is_active', 1)->orderby('created_at','desc')->with('user','categories');
      }

//Func By Viren
 public function agree(){
        return $this->belongsToMany('App\Model\ShortOpinion','short_opinion_likes','user_id','short_opinion_id','Agree_Disagree');//->where('', 1) ;
      }
 public function Disagree(){
        return $this->belongsToMany('App\Model\ShortOpinion','short_opinion_likes','user_id','short_opinion_id')->where('Agree_Disagree', 0) ;
      }

      public function liked_posts(){
        return $this->belongsToMany('App\Model\Post','likes','user_id','post_id')->where('is_active', 1)->orderby('created_at','desc')->with('user','categories');
      }

      public function likes(){
        return $this->belongsToMany('App\Model\ShortOpinion','short_opinion_likes','user_id','short_opinion_id')->where('Agree_Disagree',1);
      }

      public function liked_comments(){
        return $this->belongsToMany('App\Model\Comment','comments_likes','user_id','comment_id')->where('is_active', 1)->orderby('created_at','desc');
      }

      public function liked_thread(){
        return $this->belongsToMany('App\Model\Thread','thread_likes','user_id','thread_id');
      }

     public function followed_thread(){
       return $this->belongsToMany('App\Model\Thread','thread_followers','user_id','thread_id');
     }



      public function followers()
      {
       return $this->belongsToMany('App\Model\User', 'followers', 'leader_id', 'follower_id')->withTimestamps();
      }

      public function active_followers(){
        return $this->followers()->where('followers.is_active',1);
      }


    public function followings()
    {
      return $this->belongsToMany('App\Model\User', 'followers', 'follower_id', 'leader_id')->withTimestamps();
    }

    // public function blocked_users()
    // {
    //   return $this->belongsToMany('App\Model\BlockedUser', 'user_id', 'blocked_id')->withTimestamps();
    // }

    public function active_followings(){
      return $this->followings()->where('followers.is_active',1);
    }

     // function for adding following category by logged in user in category_followers table
     public function follow_category(){
         return $this->belongsToMany('App\Model\User','category_followers','user_id','category_id');
     }

    // function for get logged in user followed categories
     public function followed_categories(){
        return $this->hasMany('App\Model\CategoryFollower');
    }

     public function blocked_users(){
        return $this->hasMany('App\Model\BlockedUser','user_id')->with('blocked_user_info')->orderby('created_at','desc');
     }


     public function my_notifications(){
        return  $this->hasMany('App\Model\Notification','notifiable_id')
        ->leftJoin('users', function($join) {
          $join->on('notifications.data->sender_id', '=', 'users.id');
        })
        ->select('notifications.*','users.name as sender_name','users.image as sender_image','users.username as sender_username','users.unique_id as sender_unique_id')
        ->orderBy('notifications.created_at','desc')
        ->take(8)->get();
   }

   public function my_polls(){
    return $this->hasMany(Polls::class);
  }


   public function sendPasswordResetNotification($token)
   {
      $user=User::where('email',request()->email)->first();
      $token=$token.'___'.$user->email;
      $user->token=$token;
      $reseturl="http://www.weopined.com/password/reset/".$token;

      try{  Mail::send(new ResetPasswordMail($user,$reseturl));}
      catch(\Exception $e){}

   }

}
