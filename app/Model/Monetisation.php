<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Monetisation extends Model
{
    protected $table='monetisation';
    public $primaryKey='id';
    protected $fillable = ['post_id','user_id','is_monetised'];
    public $timestamps = true;

    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }

    public function post(){
        return $this->belongsTo('App\Model\Post','post_id');
    }
}
