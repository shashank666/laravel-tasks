<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\User;
use App\Model\Category;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionComment;
use DB;
use Config;

class Thread extends Model
{
    protected $table='threads';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable=['name','slug','image','description','is_active','views'];


    public function posts_relationship(){
        return $this->belongsToMany('App\Model\Post','post_threads','thread_id','post_id')->with('user:id,name,username,unique_id,image');
    }

    public function posts(){
        return $this->posts_relationship()->where(['post_threads.is_active'=>1,'posts.is_active'=>1]);
    }


    public function opinion_relationship(){
        return $this->belongsToMany('App\Model\ShortOpinion','thread_opinions','thread_id','short_opinion_id')->with('user:id,name,username,unique_id,image');
    }

    public function opinions(){
        return $this->opinion_relationship()->where(['thread_opinions.is_active'=>1,'short_opinions.is_active'=>1]);
    }


    /*public function comment_relationship(){
        return $this->hasManyThrough(ShortOpinion::class, ThreadOpinion::class);
    }*/

    public function comment(){
        return $this->hasManyThrough(ShortOpinionComment::class, ThreadOpinion::class,'thread_id','short_opinion_id','id','short_opinion_id')->where('short_opinion_comments.is_active',1)->whereNotIn('short_opinion_comments.status', [0]);
    }

    public function likes_relationship()
    {
    return $this->belongsToMany('App\Model\User', 'thread_likes','thread_id','user_id');
    }

    public function likes(){
        return $this->likes_relationship()->where('thread_likes.is_active',1);
    }

    public function followers_relationship()
    {
     return $this->belongsToMany('App\Model\User', 'thread_followers', 'thread_id', 'user_id');
    }

    public function followers(){
        return $this->followers_relationship()->where('thread_followers.is_active',1);
    }


    public function categories()
    {
    	return $this->belongsToMany('App\Model\Category','category_threads')->withTimestamps();
    }

    public function latest_threads(){
        return $this->where('is_active',1)->orderBy('created_at','desc')->take(12)->get();
    }

    public function trending_threads_with_opinion_count(){
        return DB::table('threads')
        ->leftJoin('thread_opinions','threads.id','=','thread_opinions.thread_id')
        ->select('threads.*',DB::raw('COUNT(thread_opinions.short_opinion_id) as opinionCount'))
        ->where('thread_opinions.is_active',1)
        ->where('threads.is_active',1)
        ->orderBy('opinionCount','desc')
        ->groupBy('threads.id')
        ->take(9)
        ->get();
    }


    public function get_latest_threads_with_pagination(){
        return DB::table('threads')
        ->leftJoin('thread_opinions','threads.id','=','thread_opinions.thread_id')
        ->select('threads.*',DB::raw('COUNT(thread_opinions.short_opinion_id) as opinionCount'))
        ->where('thread_opinions.is_active',1)
        ->where('threads.is_active',1)
        ->orderBy('created_at','desc')
        ->groupBy('threads.id')
        ->paginate(Config::get('app.company_ui_settings')->all_threads_pagination);
    }

    public function get_trending_threads_with_pagination(){
        return DB::table('threads')
        ->leftJoin('thread_opinions','threads.id','=','thread_opinions.thread_id')
        ->select('threads.*',DB::raw('COUNT(thread_opinions.short_opinion_id) as opinionCount'))
        ->where('thread_opinions.is_active',1)
        ->where('threads.is_active',1)
        ->orderBy('opinionCount','desc')
        ->groupBy('threads.id')
        ->paginate(Config::get('app.company_ui_settings')->all_threads_pagination);
    }

    public function scopeWithAndWhereHas($query, $relation, $constraint){
        return $query->whereHas($relation, $constraint)
                     ->with([$relation => $constraint]);
    }
}
