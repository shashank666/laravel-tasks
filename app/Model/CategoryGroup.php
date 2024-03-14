<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class CategoryGroup extends Model
{
    public $table='category_groups';
    public $primaryKey='id';

    protected $fillable = [
        'name', 'slug'
    ];

}
