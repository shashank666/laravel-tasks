<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CategoryThread extends Model
{
    protected $table='category_threads';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['thread_id','category_id','is_active'];

}
