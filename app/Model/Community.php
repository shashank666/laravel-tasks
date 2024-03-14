<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    protected $table='communities';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['name', 'cover_image', 'image','description','user_id','contest_category','is_active'];
}
