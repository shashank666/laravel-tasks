<?php 

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\User;
use App\Model\Achievements;

class UserAchievement extends Model {
    protected $table='user_achievements';
    public $primaryKey='id';
    public $timestamps=true;
    public $fillable=['user_id', 'achievements_id'];

    public function user(){
        return $this->belongsToMany('App\Model\User','user_id');
    }

    public function achievements() {
        return $this->belongsToMany('App\Model\Achievements', 'achievements_id');
    }
}