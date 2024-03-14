<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    protected $table='user_account';
    public $primaryKey='id';
   
    protected $fillable = [
        'user_id','user_email','mobile','account_no','account_holdername', 'account_type','bank_name','bank_ifsc_code', 'state','address','zip_code','city','state','country'
    ];

    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }

}
