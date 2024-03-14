<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Tag;

class PostTags extends Model
{
    protected $table='post_tags';
    public $primaryKey='id';
    protected $fillable = ['post_id', 'tag_id'];
}
