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

class DeletedUser extends Authenticatable
{
    use Notifiable;
    use HasPushSubscriptions;
    protected $table='deleted_users';
    public $primaryKey='id';
    public $timestamps = true;
    //protected $appends = ['profile_url'];

    protected $fillable = [
        'name','username','unique_id','email','phone_code','mobile',
         'password','reset_token','reset_token_expired_at','image','cover_image','provider', 'provider_id','email_verified','mobile_verified','verify_token','mobile_otp','mobile_otp_expired_at','otp_attempts','last_attempts',
         'is_active','is_subscribed','keywords','bio','gender','birthdate',
         'facebook_url','twitter_url','linkedin_url','instagram_url','youtube_channel_url','website_url'
         ,'views','platform','contacts_saved','contacts_saved_at','last_login_at','last_login_ip','deleted_at'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    

}
