<?php

namespace App\Http\Controllers\Frontend\Post;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Model\Category;
use App\Model\Thread;
use App\Model\Post;
use App\Model\User;


class SearchController extends Controller
{
    public $threads;

    public function __construct()
    {
        $colors=['#37474f','#ff9800','#0097a7','#5c007a','#00796b','#d32f2f','#5d4037','#827717','#689f38','#e91e63','#424242','#1565c0','#880e4f','#f57f17'];
        View::share('colors',$colors);
    }

    // function to search
    public function search(Request $request){
        if($request->has('q') && strlen($request->input('q')) > 0){
            $query=$request->input('q');
            $posts_result=Post::where(['status'=>1,'is_active'=>1])->where('title', 'LIKE', '%'.$query.'%')->with('user')->orderby('created_at','desc')->take(54)->get();
            //$categories_result=Category::where(['is_active'=>1])->where('name', 'LIKE', '%'.$query.'%')->get();
            $threads_result=Thread::where(['is_active'=>1])->withCount('opinions')->has('opinions','>','0')->where('name', 'LIKE',$query.'%')->take(52)->get();
            $users_result=User::where(['is_active'=>1])->where('name', 'LIKE', '%'.$query.'%')->take(52)->get();
            $bookmarked_posts=Auth::check()?$this->my_bookmarked_postids(Auth::user()->id):[];
            $liked_posts=Auth::check()?$this->my_liked_postids(Auth::user()->id):[];
            $followed_threads=Auth::check()?$this->my_followed_threadids(Auth::user()->id):[];
            $followingids= Auth::check()? auth()->user()->active_followings->pluck('id')->toArray():[];

            $menu='home';
            $active_tab=$this->get_active_tab($posts_result,$threads_result,$users_result);
            return view('frontend.posts.show.search_results',compact('active_tab','query','menu','posts_result','bookmarked_posts','liked_posts','threads_result','followed_threads','users_result','followingids'));
        }else{
            return redirect('/');
        }
    }

    protected function get_active_tab($posts_result,$threads_result,$users_result){
        if(count($posts_result)>0){
            $activeTab='posts';
        }else if(count($threads_result)>0){
            $activeTab='threads';
        }else if(count($users_result)>0){
            $activeTab='users';
        }else{
            $activeTab='no_result';
        }
        return $activeTab;
    }


    public function search_topics(Request $request){
        if($request->has('q') && strlen($request->input('q')) > 0){
        $query=$request->input('q');
        $categories_result=Category::where(['is_active'=>1])->where('name', 'LIKE', '%'.$query.'%')->get();
        $menu='category';
        $posts_result=[];
        $threads_result=[];
        $users_result=[];
        return view('frontend.posts.show.search_results',compact('posts_result','threads_result','users_result','query','menu','categories_result'));
        }else{
            if($request->ajax()){
                return response()->json(array('status'=>'error','message'=>'No Topics Found'));
            }else{
                return redirect('/');
            }
        }
    }


    public function search_users(Request $request){
        if($request->has('q') && strlen($request->input('q')) > 0){
        $menu='user';
        $active_tab='users';
        $query=$request->input('q');
        $users_result=User::where(['is_active'=>1])->where('name', 'LIKE', '%'.$query.'%')->paginate(24);
        $followingids=Auth::check()?auth()->user()->active_followings->pluck('id')->toArray():[];
        $posts_result=[];
        $threads_result=[];
        $categories_result=[];
        return view('frontend.posts.show.search_results',compact('posts_result','threads_result','categories_result','query','menu','active_tab','users_result','followingids'));
        }
        else{
            return redirect('/');
        }
    }

    public function search_threads(Request $request){
        if($request->has('q') && strlen($request->input('q')) > 0){
        $menu='thread';
        $active_tab='threads';
        $query=$request->input('q');
        $posts_result=[];
        $users_result=[];
        $categories_result=[];
        $followed_threads=Auth::check()?$this->my_followed_threadids(Auth::user()->id):[];

        if($request->ajax()){
            $threads_result=Thread::where(['is_active'=>1])->where('name', 'LIKE', '%'.$query.'%')->take(100)->get();
            return response()->json(array('status' => 'success','threads'=>$threads_result));
        }else{
            $threads_result=Thread::where(['is_active'=>1])->where('name', 'LIKE', '%'.$query.'%')->paginate(24);
            return view('frontend.posts.show.search_results',compact('posts_result','users_result','categories_result','query','menu','active_tab','threads_result','followed_threads'));
        }
        }else{
            if($request->ajax()){
                return response()->json(array('status'=>'error','message'=>'No Threads Found'));
            }else{
                return redirect('/');
            }
        }
    }


}

