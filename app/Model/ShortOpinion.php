<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Model\User;
use App\Model\ShortOpinionLike;
use App\Model\ShortOpinionComment;
use App\Model\Views;


class ShortOpinion extends Model
{

    protected $table='short_opinions';
    public $primaryKey='id';
    public $fillable = ['uuid','title','body','plain_body','cpanel_body','hash_tags','cover','cover_type','links','thumbnail','user_id','type','community_id','views','score','post_id','news_id','platform','opinion_score','updated_at'];
    public $hidden=['cpanel_body'];
    public $timestamps = true;

    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }

    public function likes()
    {
    return $this->belongsToMany('App\Model\User', 'short_opinion_likes','short_opinion_id','user_id')->where('short_opinion_likes.is_active',1);
    }

  public function Agree()
    {
    return $this->belongsToMany('App\Model\User', 'short_opinion_likes','short_opinion_id','user_id')->where('short_opinion_likes.Agree_Disagree',1);
    }
	public function Disagree()
    {
    return $this->belongsToMany('App\Model\User', 'short_opinion_likes','short_opinion_id','user_id')->where('short_opinion_likes.Agree_Disagree',0);
    }

    public function threads()
    {
      return $this->belongsToMany('App\Model\Thread','thread_opinions')->where(['threads.is_active'=>1,'thread_opinions.is_active'=>1])->withTimestamps();
    }

    public function active_thread_ids()
    {
      return $this->belongsToMany('App\Model\Thread','thread_opinions')->where(['threads.is_active'=>1,'thread_opinions.is_active'=>1])->pluck('thread_opinions.thread_id')->toArray();

    }

    public function likesCount()
    {
      return $this->hasOne('App\Model\ShortOpinionLike')
        ->selectRaw('short_opinion_id, count(*) as aggregate')
        ->where('is_active',1)
        ->groupBy('short_opinion_id');
    }
	
	 
	 public function AgreeCount()
    {
      return $this->hasOne('App\Model\ShortOpinionLike')
        ->selectRaw('short_opinion_id, count(*) as aggregate')
        ->where('Agree_Disagree',1)
        ->groupBy('short_opinion_id');
    }
	 public function DisagreeCount()
    {
      return $this->hasOne('App\Model\ShortOpinionLike')
        ->selectRaw('short_opinion_id, count(*) as aggregate')
        ->where('Agree_Disagree',0)
        ->groupBy('short_opinion_id');
    }
	
	 public function TotalCount()
    {
      return $this->hasOne('App\Model\ShortOpinionLike')
        ->selectRaw('short_opinion_id, count(*) as aggregate')         
        ->groupBy('short_opinion_id');
    }

    public function getLikesCountAttribute()
    {
      // if relation is not loaded already, let's do it first
      if (!array_key_exists('likesCount', $this->relations))
        $this->load('likesCount');
      $related = $this->getRelation('likesCount');
      // then return the count directly
      return ($related) ? (int) $related->aggregate : 0;
    }
	
	public function getAgreeCountAttribute()
    {
      // if relation is not loaded already, let's do it first
      if (!array_key_exists('AgreeCount', $this->relations))
        $this->load('AgreeCount');
      $related = $this->getRelation('AgreeCount');
      // then return the count directly
      return ($related) ? (int) $related->aggregate : 0;
    }
	
	public function getDisagreeCountAttribute()
    {
      // if relation is not loaded already, let's do it first
      if (!array_key_exists('DisagreeCount', $this->relations))
        $this->load('DisagreeCount');
      $related = $this->getRelation('DisagreeCount');
      // then return the count directly
      return ($related) ? (int) $related->aggregate : 0;
    }

    public function shares()
    {
    return $this->belongsToMany('App\Model\User', 'shares','short_opinion_id','user_id')->where('shares.is_active',1);
    }

    public function sharesCount()
    {
      return $this->hasOne('App\Model\Shares')
        ->selectRaw('short_opinion_id, count(*) as aggregate')
        ->where('is_active',1)
        ->groupBy('short_opinion_id');
    }

    public function getSharesCountAttribute()
    {
      // if relation is not loaded already, let's do it first
      if (!array_key_exists('sharesCount', $this->relations))
        $this->load('sharesCount');
      $related = $this->getRelation('sharesCount');
      // then return the count directly
      return ($related) ? (int) $related->aggregate : 0;
    }

    /*public function getTrendingCountAttribute()
    {
        return $this->shares()->count() + $this->likes()->count() + $this->comments()->count();
    }*/
    public function views()
    {
    return $this->belongsToMany('App\Model\User','views','post_id','user_id');
    }

    public function ViewsCount(){
        return $this->hasOne('App\Model\Views')
        ->selectRaw('post_id, count(*) as aggregate')
        ->where('is_active',1)
        ->groupBy('post_id');
    }

    public function getViewsCountAttribute()
    {
      // if relation is not loaded already, let's do it first
      if ( ! array_key_exists('ViewsCount', $this->relations))
        $this->load('ViewsCount');
      $related = $this->getRelation('ViewsCount');
      // then return the count directly
      return ($related) ? (int) $related->aggregate : 0;
    }

    public function comments()
    {
        return $this->hasMany('App\Model\ShortOpinionComment')->where('is_active',1)->orderBy('created_at','desc');
    }

    public function commentsCount()
    {
      return $this->hasOne('App\Model\ShortOpinionComment')
        ->selectRaw('short_opinion_id, count(*) as aggregate')
        ->where('is_active',1)->whereNotIn('status', [0])
        ->groupBy('short_opinion_id');
    }
    
    public function getCommentsCountAttribute()
    {
      if (!array_key_exists('commentsCount', $this->relations))
      $this->load('commentsCount');
      $related = $this->getRelation('commentsCount');
      return ($related) ? (int) $related->aggregate : 0;
    }
    
    public function latest_comments(){
      return $this->hasMany('App\Model\ShortOpinionComment')->where(['is_active'=>1,'parent_id'=>0])->whereNotIn('status', [0])->with('user')->take(1)->orderBy('created_at','desc');
  }


}
