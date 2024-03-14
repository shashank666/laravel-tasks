<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class NotificationMail extends Model
{
    protected $table='notification_mail';
    public $primaryKey='id';
    public $timestamps=false;
    protected $fillable = ['name','email'];

}
