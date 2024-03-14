<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Contest extends Model
{
    protected $table='contest';
    public $primaryKey='id';
    public $timestamps=false;
    protected $fillable=[ 'title','slug', 'description', 'image', 'is_active', 'start_date', 'end_date'];

    // public function posts_relationship(){
    //     return $this->belongsToMany('App\Model\Post','post_keywords','keywords_id','post_id')->with('user:id,name,username,unique_id,image');
    // }

    // public function posts(){
    //     return $this->posts_relationship()->where('post_threads.is_active',1);
    // }

}
