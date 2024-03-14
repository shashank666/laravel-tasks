<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EmailPassword extends Model
{
    protected $table='email_password';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['user_id', 'email','password'];
}
