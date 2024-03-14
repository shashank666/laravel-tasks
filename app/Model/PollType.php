<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PollType extends Model
{
    protected $table='poll_types';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['type', 'slug','description','is_active','ip_address','opt1','opt2','opt3'];

    

}
