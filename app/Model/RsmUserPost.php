<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RsmUserPost extends Model
{
    protected $table='rsm_user_post';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['post_id','user_id','total_revenue','money','payment_status' ,'is_active'];

    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }

    public function post(){
        return $this->belongsTo('App\Model\Post','post_id');
    }

}
