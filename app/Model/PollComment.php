<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\User;
use App\Model\Polls;
use Carbon\Carbon;

class PollComment extends Model
{
    protected $table='poll_comments';
    public $primaryKey='id';
    public $timestamps = true;

    protected $fillable = [
        'parent_id',
        'comment',
        'media',
        'poll_id',
        'user_id'
    ];


    public function polls(){
        return $this->belongsTo('App\Model\Polls');
    }

    public function user(){
        return $this->belongsTo('App\Model\User')->select(['id','image','name','username','unique_id']);
    }

    public function replies() {
        return $this->hasMany('App\Model\PollComment', 'parent_id')->where('is_active',1)->with('user');
    }

    public function totalreplies() {
        return $this->hasMany('App\Model\PollComment', 'parent_id')->with('user');
    }

    public function likes(){
        return $this->hasMany('App\Model\PollCommentLike','comment_id')->where('is_active',1);
    }

    public function disagree(){
        return $this->hasMany('App\Model\PollCommentDisagree','comment_id')->where('is_active',1);
    }


    public function getCreatedAtAttribute($value)
    {

         if(Carbon::parse($value)->diffInHours(Carbon::now(), false) >= 12){
            return  Carbon::parse($value)->format('j M Y , h:i A');
        }
        else{
            return  Carbon::parse($value)->diffForHumans();
        }
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('j M Y , h:i A');
         if(Carbon::parse($value)->diffInHours(Carbon::now(), false) >= 12){
            //return  Carbon::parse($value)->toFormattedDateString();
            return Carbon::parse($value)->format('j M Y , h:i A');
        }
        else{
            return  Carbon::parse($value)->diffForHumans();
        }
    }
}