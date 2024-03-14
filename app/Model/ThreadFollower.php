<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ThreadFollower extends Model
{
    protected $table='thread_followers';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['thread_id', 'user_id','is_active'];

    public function thread(){
        return $this->belongsTo('App\Model\Thread','thread_id')->withCount('opinions','comment')->where('is_active',1);
    }
}