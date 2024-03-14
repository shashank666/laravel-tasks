<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\User;
use App\Model\ShortOpinion;
use Carbon\Carbon;

class ShortOpinionComment extends Model
{

    protected $table='short_opinion_comments';
    public $primaryKey='id';
    public $timestamps = true;

    protected $fillable = [
        'parent_id',
        'comment',
        'media',
        'short_opinion_id',
        'user_id'
    ];


    public function short_opinion(){
        return $this->belongsTo('App\Model\ShortOpinion');
    }

    public function user(){
        return $this->belongsTo('App\Model\User')->select(['id','image','name','username','unique_id']);
    }

    public function replies() {
        return $this->hasMany('App\Model\ShortOpinionComment', 'parent_id')->where('is_active',1)->with('user');
    }

    public function totalreplies() {
        return $this->hasMany('App\Model\ShortOpinionComment', 'parent_id')->with('user');
    }

    public function likes(){
        return $this->hasMany('App\Model\ShortOpinionCommentLike','comment_id')->where('is_active',1);
    }

    public function disagree(){
        return $this->hasMany('App\Model\ShortOpinionCommentDisagree','comment_id')->where('is_active',1);
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
