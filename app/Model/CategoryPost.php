<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CategoryPost extends Model
{
    protected $table='category_posts';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['post_id','category_id','is_active','created_at','updated_at'];

    public function post(){
        return $this->belongsTo('App\Model\Post','post_id')->where(['is_active'=>1,'status'=>1])->with('user:id,name,username,unique_id,image','categories:category_id,name,image','threads:thread_id,name');
    }
}
