<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Post;

class ArticleStatus extends Model
{
    protected $table='article_status';
    public $primaryKey='id';
    protected $fillable = ['post_id','user_id','promo_review','backlink','plagiarism_tested'];
    public $timestamps = true;

    public function post(){
        return $this->belongsTo('App\Model\Post','post_id');
    }
    

}
