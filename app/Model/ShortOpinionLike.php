<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ShortOpinionLike extends Model
{
    public $primaryKey='id';
    protected $table='short_opinion_likes';
    protected $fillable=['user_id','short_opinion_id','Agree_Disagree','liked_at'];//liked_at
    public $timestamps = false;
    
    public function short_opinion(){
        return $this->belongsTo('App\Model\ShortOpinion','short_opinion_id')->where('is_active',1);
    }

    public function user(){
        return $this->belongsTo('App\Model\User','user_id')->where('is_active',1);
    }

}
