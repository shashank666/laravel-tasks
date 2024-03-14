<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PollRelation extends Model
{
    protected $table='poll_relation';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['rel_poll_id','poll_id','is_active'];
    

    public function polls(){
        return $this->belongsTo('App\Model\Polls','poll_id')->where(['polls.is_active'=>1]);
    }


}
