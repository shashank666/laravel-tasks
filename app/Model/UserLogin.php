<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    protected $table='user_logins';
    public $primaryKey='id';
    public $timestamps = false;
   
    protected $fillable = [
        'session_id',
        'user_id',
        'ip_address',
        'user_agent',
        'browser_name',
        'browser_version',
        'device_os_name',
        'device_name',
        'device_type',
        'is_robot',
        'location_id',
        'platform',
        'login_at',
        'is_active'
    ];

    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }

    public function location(){
        return $this->belongsTo('App\Model\UserLocation','location_id');
    }

}
