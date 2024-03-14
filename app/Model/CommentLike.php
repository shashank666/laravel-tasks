<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{

    protected $table='comments_likes';
    public $timestamps = false;
    protected $fillable = [
        'comment_id',
        'user_id',
        'is_active',
        'liked_at'
    ];

    public function user(){
        return $this->belongsTo('App\Model\User','user_id')->select('id','name','username','unique_id');
    }

    public function comment(){
        return $this->belongsTo('App\Model\Comment','comment_id');
    }

}
