<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Category extends Model
{
    protected $table='categories';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['name', 'slug', 'image','description','category_group','is_active'];

    public function followers(){
        return $this->belongsToMany('App\Model\User', 'category_followers', 'category_id', 'user_id')->where(['categories.is_active'=>1,'category_followers.is_active'=>1]);
    }

    public function posts()
    {
        return $this->belongsToMany('App\Model\Post','category_posts','category_id','post_id')->where(['categories.is_active'=>1,'posts.is_active'=>1]);
    }

    // function for getting threads by category with pagination
    public function threads()
    {
        return $this->belongsToMany('App\Model\Thread','category_threads')->where(['categories.is_active'=>1,'category_threads.is_active'=>1]);
    }



    // function for getting latest posts by category with pagination
    public function latest_posts()
    {
        return $this->belongsToMany('App\Model\Post','category_posts','category_id','post_id')
        ->where(['posts.status'=>1,'posts.is_active'=>1,'posts.platform'=>'website'])
        ->with('user','categories')
        ->withCount(['likes','comments','views'])
        ->orderBy('created_at','desc')
        ->paginate(config('app.company_ui_settings')->category_latest_posts_pagination);

    }

    // function for getting 5 most viewed posts by category
    public function get_5_most_viewed_posts(){
        return $this->belongsToMany('App\Model\Post','category_posts','category_id','post_id')
        ->where(['posts.status'=>1,'posts.is_active'=>1,'posts.platform'=>'website'])
        ->with('user')
        ->withCount(['likes','views'])
        ->orderBy('views','desc')
        ->take(5)
        ->get();
    }

    // function for getting 5 most liked posts by category
    public function get_5_most_liked_posts(){
        return $this->belongsToMany('App\Model\Post','category_posts','category_id','post_id')
        ->where(['posts.status'=>1,'posts.is_active'=>1,'posts.platform'=>'website'])
        ->with('user')
        ->withCount(['likes','views'])
        ->orderBy('likes','desc')
        ->take(5)
        ->get();
    }

    // function for getting 5 latest posts by category
    public function get_5_latest_posts(){
        return $this->belongsToMany('App\Model\Post','category_posts','category_id','post_id')
        ->where(['posts.status'=>1,'posts.is_active'=>1,'posts.platform'=>'website'])
        ->with('user')
        ->withCount(['likes','views'])
        ->orderBy('created_at','desc')
        ->take(5)
        ->get();
    }

    // function for displaying 4 latest posts by category in index page
    public function suggestions(){
        return $this->belongsToMany('App\Model\Post','category_posts','category_id','post_id')
        ->where(['posts.status'=>1,'posts.is_active'=>1,'posts.platform'=>'website'])
        ->with('user','categories')
        ->inRandomOrder()
        ->take(4)
        ->get();
    }

    // function for displaying 4 latest posts by category in index page
    public function index_posts(){
        return $this->belongsToMany('App\Model\Post','category_posts','category_id','post_id')
        ->where(['posts.status'=>1,'posts.is_active'=>1,'posts.platform'=>'website'])
        ->with('user','categories')
        ->orderBy('created_at','desc')
        ->take(4)
        ->get();
    }


    // function for get all categories
    public function get_all(){
       return $this->where('is_active', 1)->orderBy('name', 'asc')->get();
    }

}
