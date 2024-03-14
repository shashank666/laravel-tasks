<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserEarning extends Model
{
    protected $table='user_earning';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['user_id','threshold','total_earning','total_paid','payment_status','is_active'];

    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }
    
    public function user_account(){
        return  $this->belongsTo('App\Model\UserAccount','user_id','user_id');
    }

}
