<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    protected $table='employee';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['name','position','email','cmpemail','phone_code','mobile','password','dateofbirth','dateofjoin','dateofrelease','image','position','is_active'];
}
