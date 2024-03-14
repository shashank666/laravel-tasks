<?php


namespace App\Http\Controllers\Frontend\Pages;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Model\Post;


class RssController extends Controller
{
    public function articles()
    {
    //RSS FEED FOR NEWS.GOOGLE PUBLISHER

    $posts = Post::where('is_active', 1)->latest()->get();
    $site = [
      'name' => 'OPINED', // Simplest Web
      'url' => 'https://weopined.com/articles/rss', // Link to your rss.xml. eg. https://simplestweb.in/rss.xml
      'description' => 'Opinions',
      'language' => 'en-IN', // eg. en, en-IN, jp
      'lastBuildDate' => $posts[0]->created_at, // This generates the latest posts date in RSS compatible format
    ];
    return response()->view('frontend.pages.rss', [
              'posts' => $posts,'site' => $site,
          ])->header('Content-Type', 'text/xml');
    }
}