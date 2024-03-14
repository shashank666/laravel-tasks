<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    protected $table='bookmarks';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'post_id',
        'is_active',
        'bookmarked_at',
    ];
    
    public function post(){
        return $this->belongsTo('App\Model\Post')
        ->withCount('likes','views','comments')
        ->with('user:id,name,username,unique_id,image','categories:id,name,image','threads:id,name');
    }   
}