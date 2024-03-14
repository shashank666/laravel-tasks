<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ThreadLike extends Model
{
    protected $table='thread_likes';
    public $primaryKey='id';
    protected $fillable = ['thread_id', 'user_id','is_active','liked_at'];
    public $timestamps = false;
  
}
