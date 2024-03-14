<?php

namespace App\Http\Controllers\Api\Post;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Model\User;
use App\Model\Category;
use App\Model\CategoryPost;
use App\Model\Thread;
use App\Model\Post;
use App\Model\ReportPost;
use App\Model\Comment;
use App\Model\Bookmark;
use App\Model\Like;
use App\Model\ShortOpinion;
use DB;
use Config;
use Carbon\Carbon;


class PostsController extends Controller
{

    public function __construct()
    {
        $this->middleware('api');
    }

    // function for posts home screen
    public function index(Request $request){
        try{
            $user_id=-1;
            $my_liked_postids=[];
            $my_bookmarked_postids=[];

            if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                $my_liked_postids=$this->my_liked_postids($user_id);
                $my_bookmarked_postids=$this->my_bookmarked_postids($user_id);
            }

            $latest_posts=[];
            $trending_posts=[];
            $mostliked_posts=[];
            $circle_posts=[];
            $followed_category_posts=[];
            $other_category_posts=[];

            $latest_posts=Post::where(['status'=>1,'is_active'=> 1])
            ->with('user:id,name,username,unique_id,image','categories:id,name,image','threads:id,name')
            ->orderBy('created_at','desc')->take(4)->get();

            foreach($latest_posts as $index=>$post){
               
                $latest_posts[$index]=$this->formatted_test_post($post,$my_liked_postids,$my_bookmarked_postids);

            }

            $trending_posts=Post::where(['status'=>1,'is_active'=>1])
            ->whereBetween('created_at',[Carbon::now()->subDays(60),Carbon::now()])
            ->with('user:id,name,username,unique_id,image','categories:id,name,image','threads:id,name')
            ->orderBy('views','desc')->take(4)->get();

            foreach($trending_posts as $index=>$post){
                $trending_posts[$index]=$this->formatted_test_post($post,$my_liked_postids,$my_bookmarked_postids);
            }


            $mostliked_posts=Post::where(['status'=>1,'is_active'=>1])
            ->with('user:id,name,username,unique_id,image','categories:id,name,image','threads:id,name')
            ->withCount(['likes','comments'])
            ->orderBy('likes','desc')->take(4)->get();

            foreach($mostliked_posts as $index=>$post){
                $mostliked_posts[$index]=$this->formatted_test_post($post,$my_liked_postids,$my_bookmarked_postids);
            }

            $all_categoryids=DB::table('categories')->select('id')->get()->pluck('id')->toArray();

            if($user_id!=-1){
                $user=User::where(['id'=>$user_id,'is_active'=>1])->first();
                if($user){
                    $following_ids=$user->active_followings->pluck('id')->toArray();
                    $my_followed_categoryids=$this->my_followed_categoryids($user_id);
                    $other_categoryids=array_values(array_diff($all_categoryids,$my_followed_categoryids));
                    $random_categoryids=array_rand($other_categoryids,8);
                    shuffle($random_categoryids);
                    $circle_posts=Post::where(['status'=>1,'is_active'=> 1])
                    ->whereIn('user_id',$following_ids)
                    ->with('user:id,name,username,unique_id,image','categories:id,name,image','threads:id,name')
                    ->orderBy('created_at','desc')->take(4)->get();

                    foreach($circle_posts as $index=>$post){
                        $circle_posts[$index]=$this->formatted_test_post($post,$my_liked_postids,$my_bookmarked_postids);
                    }

                    $followed_category_posts=CategoryPost::select('post_id')->where('is_active',1)->whereIn('category_id',$my_followed_categoryids)->orderBy('created_at','desc')->take(4)->get();
                    foreach($followed_category_posts as $index=>$category_post){
                        $post=Post::where(['id'=>$category_post->post_id,'is_active'=>1,'status'=>1])
                        ->with('user:id,name,username,unique_id,image','categories:id,name,image','threads:id,name')
                        ->first();
                        if($post){
                            $followed_category_posts[$index]=$this->formatted_test_post($post,$my_liked_postids,$my_bookmarked_postids);
                        }
                    }

                    $other_category_post_ids=CategoryPost::select('post_id')->where('is_active',1)->whereIn('category_id',$random_categoryids)->distinct('post_id')->orderBy('created_at','desc')->take(100)->get();
                    $count=0;
                    foreach($other_category_post_ids as $index=>$category_post){
                        if($count<8){
                            $post=Post::where(['id'=>$category_post->post_id,'is_active'=>1,'status'=>1])
                            ->with('user:id,name,username,unique_id,image','categories:id,name,image','threads:id,name')
                            ->first();
                            if($post){
                                $count++;
                                $formatted=$this->formatted_test_post($post,$my_liked_postids,$my_bookmarked_postids);
                                array_push($other_category_posts,$formatted);
                            }
                        }
                    }
                }
            }else{
                $random_categoryids=array_rand($all_categoryids,8);

                $other_category_post_ids=CategoryPost::select('post_id')->where('is_active',1)->whereIn('category_id',$random_categoryids)->distinct('post_id')->orderBy('created_at','desc')->take(100)->get();
                $count=0;
                foreach($other_category_post_ids as $index=>$category_post){
                    if($count<8){
                        $post=Post::where(['id'=>$category_post->post_id,'is_active'=>1,'status'=>1])
                        ->with('user:id,name,username,unique_id,image','categories:id,name,image','threads:id,name')
                        ->first();
                        if($post){
                            $count++;
                            $formatted=$this->formatted_test_post($post,$my_liked_postids,$my_bookmarked_postids);
                            array_push($other_category_posts,$formatted);
                        }
                    }
                }
            }

            
            return response()->json([
                'status'=>'success',
                'result'=>1,
                'latest_posts'=>$latest_posts,
                'trending_posts'=>$trending_posts,
                'mostliked_posts'=>$mostliked_posts,
                'circle_posts'=>$circle_posts,
                'followed_category_posts'=>$followed_category_posts,
                'other_category_posts'=>$other_category_posts
            ]);
        }
        catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }


    // function to get latest posts
    public function get_latest_posts(Request $request){
        try{
            $user_id=-1;
            $my_liked_postids=[];
            $my_bookmarked_postids=[];

            if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                $my_liked_postids=$this->my_liked_postids($user_id);
                $my_bookmarked_postids=$this->my_bookmarked_postids($user_id);
            }

            $posts=Post::where(['status'=>1,'is_active'=> 1])
            ->with('user:id,name,username,unique_id,image','categories:id,name,image','threads:id,name')
            ->orderBy('created_at','desc')
            ->paginate(12);
            $formatted=$this->format_api_posts($posts,$my_liked_postids,$my_bookmarked_postids);
            $meta=$this->get_meta($posts);
            $response=array('status'=>'success','result'=>1,'posts_result'=>$formatted,'meta'=>$meta);
            return response()->json($response,200);
        }catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    // function to get trending posts
     public function get_trending_posts(Request $request){
        try{
            $user_id=-1;
            $my_liked_postids=[];
            $my_bookmarked_postids=[];

            if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                $my_liked_postids=$this->my_liked_postids($user_id);
                $my_bookmarked_postids=$this->my_bookmarked_postids($user_id);
            }

            $posts=Post::where(['status'=>1,'is_active'=>1])
            ->whereBetween('created_at',[Carbon::now()->subDays(60),Carbon::now()])
            ->with('user:id,name,username,unique_id,image','categories:id,name,image','threads:id,name')
            ->orderBy('views','desc')
            ->paginate(12);
            $formatted=$this->format_api_posts($posts,$my_liked_postids,$my_bookmarked_postids);
            $meta=$this->get_meta($posts);
            $response=array('status'=>'success','result'=>1,'posts_result'=>$formatted,'meta'=>$meta);
            return response()->json($response,200);
        }catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    // function to get most liked posts
    public function get_mostliked_posts(Request $request){
        try{
            $user_id=-1;
            $my_liked_postids=[];
            $my_bookmarked_postids=[];

            if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                $my_liked_postids=$this->my_liked_postids($user_id);
                $my_bookmarked_postids=$this->my_bookmarked_postids($user_id);
            }

            $posts=Post::where(['status'=>1,'is_active'=>1])
            ->with('user:id,name,username,unique_id,image','categories:id,name,image','threads:id,name')
            ->withCount(['likes','comments'])
            ->orderBy('likes','desc')
            ->paginate(12);
            $formatted=$this->format_api_posts($posts,$my_liked_postids,$my_bookmarked_postids);
            $meta=$this->get_meta($posts);
            $response=array('status'=>'success','result'=>1,'posts_result'=>$formatted,'meta'=>$meta);
            return response()->json($response,200);
        }catch(\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }


    // function to get all categories
    public function get_all_categories(Request $request){
        try{
            $my_followed_categoryids=[];
            $user_id=-1;

            $categories=Category::select('id','name','image')
            ->where(['is_active'=>1])
            ->get();

            if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                $user=User::where(['id'=>$user_id,'is_active'=>1])->first();
                if($user){
                   $my_followed_categoryids=$this->my_followed_categoryids($user_id);
                }
            }

            foreach($categories as $index=>$category){
                $categories[$index]['is_followed']=in_array($category->id,$my_followed_categoryids)?1:0;
            }
            $response=array('status'=>'success','categories_result'=>$categories);
            return response()->json($response,200);
        }catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    // function to get posts by category id
    public function get_posts_by_category(Request $request,$id){
        try{
            $category=Category::where(['id'=>$id,'is_active'=>1])->first();
            $posts=$category->latest_posts();
            $response=array('status'=>'success','result'=>1,'posts_result'=>$posts);
            return response()->json($response,200);
        }catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }


}
