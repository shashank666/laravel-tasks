<?php 

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\User;
use App\Model\Comment;
use App\Model\Like;
use App\Model\Post;
use App\Model\Shares;
use App\Model\UserAchievements;
use App\Model\ShortOpinionLike;

class Point extends Model {
    protected $table='points';
    public $primaryKey='id';
    public $timestamps=true;
    public $fillable=['user_id', 'agree_points', 'reward_points', 'comment_points', 'post_points', 'share_points','follower_points','daily_points'];

    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }

    public function shares() {
        return $this->hasMany(Shares::class);
    }

    public function followers() {
       return $this->belongsToMany('App\Model\User', 'followers', 'leader_id', 'follower_id')->withTimestamps();
    }

    public function active_followers() {
       return $this->followers()->where('followers.is_active',1);
    }
}