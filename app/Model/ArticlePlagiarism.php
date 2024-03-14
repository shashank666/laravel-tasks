<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ArticlePlagiarism extends Model
{
    protected $table='article_plagiarism';
    public $primaryKey='id';
    protected $fillable = ['post_id','process_id','title','introduction','comparison_report','cached_version','embeded_comparison','plagiarism_percents','plagiarised_words'];
    public $timestamps = true;


    

}
