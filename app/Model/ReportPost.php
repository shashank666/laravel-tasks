<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ReportPost extends Model
{
    protected $table='report_posts';
    public $primaryKey='id';
    protected $fillable=['post_id','reported_user_id','report_flag','report_reason','is_active','mark_read'];
    public $timestamps = true;

    public function user(){
        return $this->belongsTo('App\Model\User','reported_user_id');
    }

    public function post(){
        return $this->belongsTo('App\Model\Post','post_id');
    }

    public function opinion(){
        return $this->belongsTo('App\Model\ShortOpinion','post_id');
    }

}
