<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ReportComment extends Model
{
    protected $table='report_comment';
    public $primaryKey='id';
    protected $fillable=['short_comment_id','post_comment_id','reported_user_id','report_flag','report_reason','is_active'];
    public $timestamps = false;

    public function user_new(){
        return $this->belongsTo('App\Model\User','reported_user_id');
    }

    public function comment(){
        return $this->belongsTo('App\Model\ShortOpinionComment','comment_id');
    }
/*
    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }
*/}  
