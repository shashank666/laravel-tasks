<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PostKeywords extends Model
{
    protected $table='post_keywords';
    public $primaryKey='id';
    public $timestamps=true;
    protected $fillable = ['post_id', 'keyword_id','is_active'];

}
