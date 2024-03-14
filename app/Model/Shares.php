<?php

namespace App\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Shares extends Model
{
    protected $table='shares';
    public $primaryKey='id';
    public $timestamps = false;
    //protected $with = 'replies';
    protected $fillable = [
        'short_opinion_id',
        'post_id',
        'user_id',
        'plateform',
        'ip_address'
    ];
    public function short_opinion(){
        return $this->belongsTo('App\Model\ShortOpinion','short_opinion_id')->with('user')->where('is_active',1);
    }

    public function user(){
        return $this->belongsTo('App\Model\User','user_id')->where('is_active',1);
    }
    public function post(){
        return $this->belongsTo('App\Model\Post','post_id')->with('user')->where('is_active',1);
    }
}
