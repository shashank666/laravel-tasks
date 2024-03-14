<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    protected $table='followers';
    public $primaryKey='id';
    public $timestamps = true;

    protected $fillable = [
        'follower_id', 'leader_id',
    ];

    public function leader(){
        return $this->belongsTo('App\Model\User','leader_id')->select('users.id','users.name','users.username','users.unique_id','users.image','users.bio');
    }

    public function follower(){
        return $this->belongsTo('App\Model\User','follower_id')->select('users.id','users.name','users.username','users.unique_id','users.image','users.bio');
    }


}
