<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsHeadlines extends Model
{

    protected $table = 'news_headlines';

    protected $fillable = [
        'title',
        'author',
        'description',
        'url',
        'url_to_image',
        'created_at',
        'published_at'
    ];

    public function short_opinions()
    {
        return $this->hasMany(ShortOpinion::class, 'news_id');
    }
}
