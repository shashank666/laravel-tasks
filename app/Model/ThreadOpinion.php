<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ThreadOpinion extends Model
{
    protected $table='thread_opinions';
    public $primaryKey='id';
    protected $fillable = ['thread_id','short_opinion_id'];
    public $timestamps = true;


    public function latest_opinion(){
        return $this->belongsTo('App\Model\ShortOpinion','short_opinion_id')->withCount('comments')->where(['short_opinions.is_active'=>1,'short_opinions.community_id'=>0])->with('user:id,name,username,unique_id,image');
    }

    public function mostliked_opinion(){
        return $this->belongsTo('App\Model\ShortOpinion','short_opinion_id')->where(['short_opinions.is_active'=>1])->with('user:id,name,username,unique_id,image');
    }

    public function thread(){
        return $this->belongsTo('App\Model\Thread','thread_id')->withCount('opinions','comment')->where('is_active',1);
    }
    public function likes()
    {
    return $this->belongsToMany('App\Model\ShortOpinionLike', 'short_opinions','id','id','short_opinion_id','short_opinion_id')->where('short_opinion_likes.is_active',1);
    }


}
