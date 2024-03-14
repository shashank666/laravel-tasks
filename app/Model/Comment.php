<?php

namespace App\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table='comments';
    public $primaryKey='id';
    public $timestamps = true;
    //protected $with = 'replies';
    protected $fillable = [
        'parent_id',
        'comment',
        'media',
        'post_id',
        'user_id'
    ];


    public function post(){
        return $this->belongsTo('App\Model\Post');
    }

    public function user(){
        return $this->belongsTo('App\Model\User')->select(['id','image','name','username','unique_id']);
    }

    public function replies() {
        return $this->hasMany('App\Model\Comment', 'parent_id')->with('user');
    }

    public function likes(){
        return $this->hasMany('App\Model\CommentLike','comment_id')->where('is_active',1);
    }

     public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('j M Y , h:i A');
       /*  if(Carbon::parse($value)->diffInHours(Carbon::now(), false) >= 24){
            return  Carbon::parse($value)->toFormattedDateString();
        }
        else{
            return  Carbon::parse($value)->diffForHumans();
        } */

    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('j M Y , h:i A');
       /*  if(Carbon::parse($value)->diffInHours(Carbon::now(), false) >= 24){
            //return  Carbon::parse($value)->toFormattedDateString();
            return Carbon::parse($value)->format('j F Y , h:i A');
        }
        else{
            return  Carbon::parse($value)->diffForHumans();
        } */
    }
}
