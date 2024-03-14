<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table='tags';
    public $primaryKey='id';

    protected $fillable = ['name', 'slug'];
    
    public function posts()
    {
        return $this->belongsToMany('App\Model\Post','post_tags','tag_id', 'post_id')->where(['status'=>1,'is_active'=>1])->with('user','categories')->orderBy('created_at','desc')->paginate(12);
    }

    public function get_all(){
        return $this->where('is_active', 1)->orderBy('name', 'asc')->get();
    }

    public function search($query){
        return  $this->where('is_active', 1)->where('name', 'like', '%' . $query . '%')->get();
    }

}
