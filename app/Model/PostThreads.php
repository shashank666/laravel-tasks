<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PostThreads extends Model
{
    protected $table='post_threads';
    public $primaryKey='id';
    protected $fillable = ['post_id', 'thread_id','is_active'];
}
