<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdClick extends Model
{
    protected $table='adclick';
    public $primaryKey='id';
    protected $fillable = ['post_id','user_id','ip_address','clicked_at'];
    public $timestamps = false;


    

}
