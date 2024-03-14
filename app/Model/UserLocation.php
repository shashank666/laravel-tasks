<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    protected $table='user_location';
    public $primaryKey='id';
    public $timestamps = false;
   
    protected $fillable = [
        'user_id','city','state','country_name','country_code','postal','latitude','longitude'
    ];

    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }
}
