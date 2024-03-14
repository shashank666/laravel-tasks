<?php 

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model {
    protected $table='achievements';
    public $primaryKey='id';
    public $timestamps=true;
    public $fillable=['title', 'description', 'icon', 'reward'];

    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }
}