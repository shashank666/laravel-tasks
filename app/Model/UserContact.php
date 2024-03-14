<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserContact extends Model
{
    public $table='user_contacts';
    public $primaryKey='id';
    protected $fillable = [
        'id','name','number','normalized_number','email','is_primary','is_starred','times_contacted','last_time_contacted','type','label','user_id','app_installed','invite_hidden','follow_hidden','is_active'
    ];
    public $timestamps = true;

    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }
}
