<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $table='likes';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'post_id',
        'is_active',
        'liked_at',
        'ip_address',
        'user_agent'
    ];

   
    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }

    public function post(){
        return $this->belongsTo('App\Model\Post','post_id')->with('categories','user');
    }
}
