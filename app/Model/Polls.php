<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Model\PollComment;

class Polls extends Model
{
    protected $table='polls';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['user_id','title', 'slug','description','is_active','ip_address','poll_type','polltype_id','end_date'];

    
    public function locations(){
        return $this->hasMany('App\Model\UserLocation', 'user_id', 'user_id');
    }
    public function pollresults(){
        return $this->hasMany('App\Model\PollResults', 'poll_id', 'id');
    }
    public function threads(){
        return $this->belongsToMany('App\Model\Thread','poll_thread','id','thread_id')->where('threads.is_active',1);
    }
    public function mcpsoptions(){
        return $this->belongsToMany('App\Model\PollMultipleChoiceOption','polls','id','id')->where('polls.is_active',1);
    }
    public function comments()
    {
        return $this->hasMany('App\Model\PollComment')->where('is_active',1)->orderBy('created_at','desc');
    }
    public function commentsCount()
    {
      return $this->hasOne('App\Model\PollComment')
        ->selectRaw('poll_id, count(*) as aggregate')
        ->where('is_active',1)
        ->groupBy('poll_id');
    }
    // public function getcommentsCountAttribute()
    // {
        
      
    //   if (!array_key_exists('commentsCount', $this->relations))
    //   $this->load('commentsCount');
    //   echo "test2";
      
    //   $related = $this->getRelation('commentsCount');
    //   return ($related) ? (int) $related->aggregate : 0;
    // }
    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }
    // public function owned_by(User $user) {
    //     return $user->id==$this->user_id;
    // }
    
}
