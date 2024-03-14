<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserInvoice extends Model
{
    protected $table='user_invoice';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['user_id','paid','payment_refrence_number','billing_id','is_active'];

    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }

    public function user_earning(){
        return  $this->belongsTo('App\Model\UserEarning','user_id','user_id');
    }
    public function user_account(){
        return  $this->belongsTo('App\Model\UserAccount','user_id','user_id');
    }
}
