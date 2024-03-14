<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CommunityMember extends Model
{
    protected $table='community_members';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['community_id','user_id','joined_at'];
}