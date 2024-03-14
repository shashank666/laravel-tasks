<?php

namespace App\Repository;
use Carbon\Carbon;
use App\Model\ShortOpinion;
use DB;
use App\Model\ThreadFollower;
use App\Model\Thread;
use App\Model\ThreadOpinion;
use App\Model\Category;
use App\Model\CategoryPost;
use App\Model\CategoryThread;
use App\Model\Post;
use Illuminate\Support\Facades\Auth;


class Articles
{

    CONST CACHE_KEY='ARTICLES';

    public function get_category_posts($all_categoryids)
    {
        $key = "get_category_posts";
        $cacheKey = $this->getCacheKey($key);

        return cache()->remember($cacheKey,Carbon::now()->addMinutes(5),function() use($all_categoryids) {
            return CategoryPost::select('post_id')->distinct(['post_id'])->where('is_active',1)->whereIn('category_id',$all_categoryids)->orderBy('created_at','desc')->take(12)->get();
        });

    }

    public function get_all_categoryids()
    {
        $key = "get_all_categoryids";
        $cacheKey = $this->getCacheKey($key);
        
        return cache()->remember($cacheKey,Carbon::now()->addMinutes(5),function() {
        return DB::table('categories')->select('id')->get()->pluck('id')->toArray();
        });
    }
    public function get_other_posts($other_category_post)
    {
        $key = "get_other_posts";
        $cacheKey = $this->getCacheKey($key);
        
        return cache()->remember($cacheKey,Carbon::now()->addMinutes(5),function() use($other_category_post) {
           return Post::where(['id'=>$other_category_post->post_id,'is_active'=>1,'status'=>1])->with('user:id,name,username,unique_id,image')->first();
        });
    }

    public function trending_posts()
    {
        $key = "trending_posts";
        $cacheKey = $this->getCacheKey($key);
        $from=Carbon::now()->subDays(30);
        $to=Carbon::now();

        return cache()->remember($cacheKey,Carbon::now()->addMinutes(5),function() use($from,$to) {
        return Post::where(['status'=>1,'is_active'=>1,'platform'=>'website'])->whereBetween('created_at',[$from,$to])->with('user','categories')->orderBy('views','desc')->take(4)->get();
        });
    }
    public function mostliked_posts()
    {
        $key = "mostliked_posts";
        $cacheKey = $this->getCacheKey($key);

        return cache()->remember($cacheKey,Carbon::now()->addMinutes(5),function()  {
        return Post::where(['status'=>1,'is_active'=>1,'platform'=>'website'])->with('user','categories')->orderBy('likes','desc')->take(4)->get();
        });
    }

    public function other_category_posts_ids($other_categoryids)
    {
        $key = "other_category_posts_ids";
        $cacheKey = $this->getCacheKey($key);

        return cache()->remember($cacheKey,Carbon::now()->addMinutes(5),function() use($other_categoryids) {
        return CategoryPost::select('post_id')->distinct(['post_id'])->where('is_active',1)->whereIn('category_id',$other_categoryids)->orderBy('created_at','desc')->take(6)->get();
        });
    }

    public function getCacheKey($key)
    {
        $key = strtoupper($key);

        return self::CACHE_KEY. ".$key";
    }

}