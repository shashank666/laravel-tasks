<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BlockedUser extends Model
{
    protected $table='blocked_users';
    public $primaryKey='id';
    protected $fillable = [
        'user_id',
        'blocked_id',
        'created_at',
        'updated_at',
    ];

    public function blocked_user_info(){
        return $this->belongsTo('App\Model\User','blocked_userid');
    }
}
