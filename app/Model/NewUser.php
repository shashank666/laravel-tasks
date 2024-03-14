<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class NewUser extends Model
{
    protected $table='new_users';
    public $primaryKey='id';
    public $timestamps = true;

    protected $fillable = [
        'name','email','phone_code','mobile', 'password','lpass','mobile_verified','mobile_otp','mobile_otp_expired_at','otp_attempts','last_attempts'
    ];

    protected $hidden = [
        'password','lpass',
    ];

}
