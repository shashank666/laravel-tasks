<?php

namespace App\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\Bookmark;
use App\Model\User;
use App\Model\Comment;
use App\Model\Like;
use App\Model\Views;
use App\Model\ArticleStatus;

class Post extends Model
{
    //
    protected $table='posts';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['title','slug','uuid','coverimage','cover_type','body','plainbody','readtime','status','is_active','user_id','views','likes','plagiarism_checked','is_plagiarized','plagiarism_percentage','platform'];
    public $appends=['is_liked','is_bookmarked'];

    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }

   /*  public function category()
    {
    	return $this->belongsTo('App\Model\Category','category_id');
    }
    public function categories()
    {
    	return $this->belongsToMany('App\Model\Category','category_posts')->withTimestamps();
    }
    */

    public function categories()
    {
        return $this->belongsToMany('App\Model\Category','category_posts','post_id','category_id')->withTimestamps();
    }


    public function getCreatedAtAttribute($value)
    {

       // if(Carbon::parse($value)->diffInHours(Carbon::now(), false) >= 24){
            //return Carbon::parse($value)->format('jS F , Y');
            return Carbon::parse($value)->format("M j , Y");
       // }else{
       //    return Carbon::parse($value)->diffForHumans();
       // }
    }

    public function getShareUrlsAttribute(){
        return array(
            'twitter'=>"https://twitter.com/share?text=".$this->title."&url=https://www.weopined.com/opinion/".$this->slug,
            'facebook'=>"https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/opinion/".$this->slug,
            'linkedin'=>"https://www.linkedin.com/shareArticle?mini=true&url=https://www.weopined.com/opinion/".$this->slug,
            'whatsapp'=>"https://api.whatsapp.com/send?&text=".$this->title." .....Read more at Opined : https://www.weopined.com/opinion/".$this->slug,
            'url'=>"https://www.weopined.com/opinion/".$this->slug
        );
    }


    public function likedBy(){
        $user_id=Auth::guard('web')?auth()->user()->id:auth()->user()->user_id;
        return $this->belongsToMany('App\Model\User', 'likes', 'post_id', 'user_id')->where('user_id',$user_id);
    }

    public function getIsLikedAttribute()
    {
        if(Auth::check()){
            if ( ! array_key_exists('likedBy', $this->relations))
            $this->load('likedBy');
            $likes = $this->getRelation('likedBy');
            $liked = count($likes) > 0? 1:0;
            unset($this->likedBy);
            return $liked;
        }else{
            return 0;
        }
    }

    public function bookmarkedBy(){
        $user_id=Auth::guard('web')?auth()->user()->id:auth()->user()->user_id;
        return $this->belongsToMany('App\Model\User', 'bookmarks', 'post_id', 'user_id')->where('user_id',$user_id);
    }

    public function getIsBookmarkedAttribute()
    {
        if(Auth::check()){
            if ( ! array_key_exists('bookmarkedBy', $this->relations))
            $this->load('bookmarkedBy');
            $bookmarks = $this->getRelation('bookmarkedBy');
            $bookmarked = count($bookmarks) > 0? 1:0;
            unset($this->bookmarkedBy);
            return $bookmarked;
        }else{
            return 0;
        }
    }


    public function threads(){
        return $this->belongsToMany('App\Model\Thread','post_threads','post_id','thread_id')->where('post_threads.is_active',1);
    }

    public function keywords(){
        return $this->belongsToMany('App\Model\Keyword','post_keywords','post_id','keyword_id')->where('post_keywords.is_active',1);
    }

     public function bookmarks()
     {
     return $this->belongsToMany('App\Model\User', 'bookmarks','post_id','user_id');
     }

    /* public function is_bookmarked(User $user){
        return $this->bookmarks->contains($user);
    } */

    public function get_latest(){
        return $this->where(['status'=>1,'is_active'=>1,'platform'=>'website'])->with('user','categories')->orderBy('created_at','desc')->paginate(4);
    }

    public function likes()
    {
    return $this->belongsToMany('App\Model\User', 'likes','post_id','user_id');
    }

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

    public function likesCount(){
        return $this->hasOne('App\Model\Like')
        ->selectRaw('post_id, count(*) as aggregate')
        ->where('is_active',1)
        ->groupBy('post_id');
    }

    public function getLikesCountAttribute()
    {
      // if relation is not loaded already, let's do it first
      if ( ! array_key_exists('likesCount', $this->relations))
        $this->load('likesCount');
      $related = $this->getRelation('likesCount');
      // then return the count directly
      return ($related) ? (int) $related->aggregate : 0;
    }

    public function bookmarksCount(){
        return $this->hasOne('App\Model\Bookmark')
        ->selectRaw('post_id, count(*) as aggregate')
        ->where('is_active',1)
        ->groupBy('post_id');
    }

    public function getBookmarksCountAttribute()
    {
      // if relation is not loaded already, let's do it first
      if ( ! array_key_exists('bookmarksCount', $this->relations))
        $this->load('bookmarksCount');
      $related = $this->getRelation('bookmarksCount');
      // then return the count directly
      return ($related) ? (int) $related->aggregate : 0;
    }



    public function comments()
    {
        return $this->hasMany('App\Model\Comment')->where('is_active',1)->orderBy('created_at','desc');
    }

    public function parentComments()
    {
        return $this->comments()->where(['parent_id'=>0,'is_active'=>1]);
    }

    public function commentsCount()
    {
      return $this->hasOne('App\Model\Comment')
        ->selectRaw('post_id, count(*) as aggregate')
        ->where('is_active',1)
        ->groupBy('post_id');
    }

    public function getCommentsCountAttribute()
    {
      // if relation is not loaded already, let's do it first
      if ( ! array_key_exists('commentsCount', $this->relations))
        $this->load('commentsCount');
      $related = $this->getRelation('commentsCount');
      // then return the count directly
      return ($related) ? (int) $related->aggregate : 0;
    }

    public function shares()
    {
    return $this->belongsToMany('App\Model\User', 'shares','post_id','user_id')->where('shares.is_active',1);
    }

    public function sharesCount()
    {
      return $this->hasOne('App\Model\Shares')
        ->selectRaw('post_id, count(*) as aggregate')
        ->where('is_active',1)
        ->groupBy('post_id');
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

    public function articleStatus(){
        return $this->hasOne('App\Model\ArticleStatus','post_id');
    }
}
