<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    protected $table='keywords';
    public $primaryKey='id';
    public $timestamps=true;
    protected $fillable=['name','slug','is_active'];

    public function posts_relationship(){
        return $this->belongsToMany('App\Model\Post','post_keywords','keywords_id','post_id')->with('user:id,name,username,unique_id,image');
    }

    public function posts(){
        return $this->posts_relationship()->where('post_threads.is_active',1);
    }

}
