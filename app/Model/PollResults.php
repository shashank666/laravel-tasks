<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PollResults extends Model
{
    protected $table='poll_results';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['user_id','poll_id', 'slug','voting_type','voting','is_active','ip_address'];

    
    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }

    public function polls(){
        return $this->belongsTo('App\Model\Polls','poll_id');
    }

    public function locations(){
        return $this->hasMany('App\Model\UserLocation', 'user_id', 'user_id');
    }
    
    public function multitest()
    {
    	return $this->belongsTo('App\Model\PollMultipleChoiceOption','poll_id','poll_id','poll_id','poll_id','poll_id','poll_id');
    }
    public function mcpsoptions()
    {
    	return $this->belongsTo('App\Model\PollMultipleChoiceOption','mcps_id');
	}
}
