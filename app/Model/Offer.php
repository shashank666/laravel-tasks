<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $table='offers';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['name',
    'offer_poster_1',
    'offer_poster_2',
    'eligible_posts',
    'eligible_users',
    'likes',
    'views',
    'min_word_count',
    'max_plagiarism',
    'start_date',
    'end_date'];
}
