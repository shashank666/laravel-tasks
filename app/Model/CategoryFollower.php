<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CategoryFollower extends Model
{
    protected $table='category_followers';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['category_id', 'user_id','is_active'];

    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }

    public function category(){
        return $this->belongsTo('App\Model\Category','category_id');
    }
}
