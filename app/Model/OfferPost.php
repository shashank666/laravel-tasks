<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OfferPost extends Model
{
    protected $table='offer_posts';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['offer_id','post_id','user_id','payment_status','is_active'];

    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }

    public function post(){
        return $this->belongsTo('App\Model\Post','post_id');
    }

}
