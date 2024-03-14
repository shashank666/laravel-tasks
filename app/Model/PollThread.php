<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PollThread extends Model
{
    protected $table='poll_thread';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['thread_id','poll_id','is_active'];
    

    public function polls(){
        return $this->belongsTo('App\Model\Polls','poll_thread')->where(['polls.is_active'=>1]);
    }

    public function threads(){
        return $this->belongsToMany('App\Model\Thread','poll_thread','id','thread_id')->where('threads.is_active',1);
    }
    


}
