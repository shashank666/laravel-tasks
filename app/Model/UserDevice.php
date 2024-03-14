<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $table = 'user_devices';
    protected $fillable = [ 'user_id','device_id','api_token','gcm_token','device_brand','device_model','device_manufacturer','device_sdk_version','device_os_version','device_os_name','device_serial','app_version','is_active'];
    public $timestamps = true;

	public function user (){
        return $this->belongsTo('App\Model\User', 'user_id', 'id');
    }
    public function locations(){
        return $this->hasMany('App\Model\UserLocation', 'user_id', 'id');
    }
}
