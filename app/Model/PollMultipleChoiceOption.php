<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PollMultipleChoiceOption extends Model
{
    protected $table='poll_multiple_choice_options';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['options','poll_id','is_active'];
    

    public function polls(){
        return $this->belongsToMany('App\Model\Polls','polls','id','poll_id')->where(['polls.is_active'=>1]);
    }

    public function poll_result(){
        return $this->hasMany('App\Model\PollResults','mcps_id');
    }


}
