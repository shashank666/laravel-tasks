<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RsmOffer extends Model
{
    protected $table='rsm_offer';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['post_id','user_id','offer_sync_status','is_active'];

    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }

    public function post(){
        return $this->belongsTo('App\Model\Post','post_id');
    }

}
