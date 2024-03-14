<?php


namespace App\Http\Controllers\Frontend\Pages;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Post;
use App\Model\Category;
use App\Model\Thread;
use App\Model\ShortOpinion;
use App\Model\Polls;

class SitemapController extends Controller
{
    public function index()
    {
        return response()->view('frontend.pages.sitemap.index')->header('Content-Type', 'text/xml');
    }

    public function posts()
    {
        $posts = Post::select('id','title','coverimage','slug','created_at','updated_at')->where(['is_active'=>1,'status'=>1])->orderBy('created_at','desc')->get();
        return response()->view('frontend.pages.sitemap.posts', [
            'posts' => $posts,
        ])->header('Content-Type', 'text/xml');
    }

    public function categories()
    {
        $categories = Category::where('is_active',1)->get();
        return response()->view('frontend.pages.sitemap.categories', [
            'categories' => $categories,
        ])->header('Content-Type', 'text/xml');
    }

    public function threads(){
        $threads = Thread::where('is_active',1)->orderBy('created_at','desc')->get();
        return response()->view('frontend.pages.sitemap.threads', [
            'threads' => $threads,
        ])->header('Content-Type', 'text/xml');
    }

    public function opinions(){
        $opinions = ShortOpinion::where('is_active',1)->with('user')->orderBy('created_at','desc')->get();
       //var_dump($opinions->user);
        return response()->view('frontend.pages.sitemap.opinions', [
            'opinions' => $opinions,
        ])->header('Content-Type', 'text/xml');
    }

    public function polls(){
       
        $polls = Polls::where(['is_active'=>1,'visibility'=>1])->orderBy('created_at','desc')->get();
       
        return response()->view('frontend.pages.sitemap.polls', [
            'polls' => $polls,
        ])->header('Content-Type', 'text/xml');
    }
    public function article()
    {
        $news = Post::select('id','title','coverimage','slug','created_at','updated_at')->where(['is_active'=>1,'status'=>1])->orderBy('created_at','desc')->get();
        return response()->view('frontend.pages.sitemap.news', [
            'news' => $news,
        ])->header('Content-Type', 'text/xml');
    }
}

