<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Views extends Model
{
    protected $table='views';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'post_id',
        'opinion_id',
        'ip_address',
        'is_active',
        'viewed_at',
    ];

}
