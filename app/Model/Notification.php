<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    protected $table='notifications';
    public $primaryKey='id';
    public $timestamps=true;
    protected $fillable = ['type','	notifiable_id', 'notifiable_type','data','read_at'];

    public function getDataAttribute($value){
        return json_decode($value);
    }

    public function getCreatedAtAttribute($value)
    {
         if(Carbon::parse($value)->diffInHours(Carbon::now(), false) >= 12){
            return  Carbon::parse($value)->format('j M Y , h:i A');
        }
        else{
            return  Carbon::parse($value)->diffForHumans();
        }
    }

}
