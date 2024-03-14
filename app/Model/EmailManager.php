<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EmailManager extends Model
{
    protected $table='email_manager';
    public $primaryKey='id';
    public $timestamps=false;
    protected $fillable = ['email_to_type','email_to', 'email_subject','email_content','created_at','scheduled_at','is_active','status','job_id','error_message'];

}
