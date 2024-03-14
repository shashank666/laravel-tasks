<?php

namespace App\Http\Controllers\Admin\Post;

include_once($_SERVER['DOCUMENT_ROOT'].'/../vendor/copyleaks/php-plagiarism-checker/autoload.php');
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Model\Category;
use App\Model\Post;
use App\Model\OfferPost;
use App\Model\Like;
use App\Model\Thread;
use App\Model\Keyword;
use App\Model\PostThreads;
use App\Model\CategoryThread;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionComment;
use App\Model\ShortOpinionLike;
use App\Model\ThreadOpinion;
use App\Model\ReportPost;
use App\Model\Comment;
use App\Model\Bookmark;
use App\Model\User;
use App\Model\Follower;
use App\Model\Notification;
use App\Model\CommentLike;
use App\Model\ArticleStatus;
use App\Model\ArticlePlagiarism;
use App\Model\Monetisation;
use App\Model\RsmUserPost;
use App\Model\UserEarning;
use DB;
use Session;
use Carbon\Carbon;
use App\Http\Helpers\MailJetHelper;

use Copyleaks\CopyleaksCloud;
use Copyleaks\CopyleaksProcess;
use Requests;
use Mail;
use App\Mail\OfferPost\PlagiarismMail;
use App\Mail\OfferPost\PaymentMail;

class PostsController extends Controller
{


    public function __construct()
    {   
        $this->middleware('auth:admin');
        View::share('menu','posts');
    }



     // function for post listing with sort and filters
     public function showPosts(Request $request)
     {

         $from=$request->has('from')?$request->query('from'):Carbon::create(2018, 1, 01, 0, 0, 0);
         $to=$request->has('to')?$request->query('to'):Carbon::now()->endOfDay();
         $platform=$request->has('platform')?$request->query('platform'):'website,android';
         $status=$request->has('status')?$request->query('status'):'0,1,2';
         $is_active=$request->has('is_active')?$request->query('is_active'):'0,1';
         $plagiarism_checked=$request->has('plagiarism_checked')?$request->query('plagiarism_checked'):'0,1';
         //$selected_categories = [];
         $likes=$request->has('likes')?$request->query('likes'):'0';
         $likes_operator=$request->has('likes_operator')?$request->query('likes_operator'):'>=';
         $views=$request->has('views')?$request->query('views'):'0';
         $views_operator=$request->has('views_operator')?$request->query('views_operator'):'>=';
         $comments=$request->has('comments_count')?$request->query('comments_count'):'0';
         $comments_operator=$request->has('comments_operator')?$request->query('comments_operator'):'>=';

         $sortBy=$request->has('sortBy')?$request->query('sortBy'):'created_at';
         $sortOrder=$request->has('sortOrder')?$request->query('sortOrder'):'desc';

         $limit=$request->has('limit')?$request->query('limit'):24;
         $page=$request->has('page')?$request->query('page'):1;

         $filter_categories=$request->has('categories')?explode(',',trim($request->query('categories'))):[];
         $filter_threads=$request->has('threads')?explode(',',trim($request->query('threads'))):[];


         $searchQuery=$request->has('searchQuery') && strlen(trim($request->input('searchQuery')))>0 ? trim($request->input('searchQuery')):'';
         $searchBy=$request->has('searchBy')?$request->input('searchBy'):'title';
         $DBsearchQuery=$searchBy=='id' || $searchBy=='user_id'? $searchQuery:'%'.$searchQuery.'%';

        $query = Post::query();
        $query->whereIn('platform',explode(',',$platform));
        $query->whereIn('status',explode(',',$status));
        $query->whereIn('is_active',explode(',',$is_active));
        $query->whereIn('plagiarism_checked',explode(',',$plagiarism_checked));
        $query->with('user','categories','articleStatus');

        if(isset($DBsearchQuery) && !empty($DBsearchQuery) && strlen($DBsearchQuery)>0){
            if($searchBy=='id' || $searchBy=='user_id'){
                $query->where($searchBy, '=', $DBsearchQuery);
            }else if($searchBy=='user_name'){
                $query->whereHas('user', function ($q) use ($DBsearchQuery) {
                    $q->where('name', 'LIKE', '%'.$DBsearchQuery.'%');
                 });
            }else{
                $query->where($searchBy, 'LIKE', $DBsearchQuery);
            }
        }

        if($filter_categories && $filter_categories[0]){
            $query->whereHas('categories', function ($q) use ($filter_categories) {
                $q->whereIn('categories.id',$filter_categories);
            });
        }

        if($filter_threads && $filter_threads[0]){
            $query->whereHas('threads', function ($q) use ($filter_threads) {
                $q->whereIn('threads.id',$filter_threads);
            });
        }

        $query->orderBy($sortBy,$sortOrder);
        $posts = $query->paginate($limit);


        if($request->has('likes') && !empty($request->query('likes'))){
            $query->where('likes',$likes_operator,$likes);
        }

        if($request->has('views') && !empty($request->query('views'))){
            $query->where('views',$views_operator,$views);
        }

        if($request->has('comments_count') && !empty($request->query('comments_count'))){
            $query->has('comments_count',$comments_operator,$comments);
        }


        $count['active']=DB::table('posts')->where('is_active',1)->count();
        $count['disabled']=DB::table('posts')->where('is_active',0)->count();
        $count['total']=DB::table('posts')->count();
        $selected_categories = [];
         if($request->ajax()){
            $view = (String) view('admin.dashboard.post.post_row',compact('posts'));
            return response()->json(['html'=>$view]);
        }else{
         return view('admin.dashboard.post.index',
         compact('count','posts','from','to','selected_categories',
         'platform','status','is_active','plagiarism_checked',
         'likes','likes_operator',
         'views','views_operator',
         'comments','comments_operator',
         'sortBy','sortOrder','limit','searchBy','searchQuery','filter_categories','filter_threads'));
        }
     }

      // function for post listing with sort and filters
     public function allComment(Request $request)
     {

         $from=$request->has('from')?$request->query('from'):Carbon::create(2018, 1, 01, 0, 0, 0);
         $to=$request->has('to')?$request->query('to'):Carbon::now()->endOfDay();
         $comments=Comment::with('post','user')->orderBy('created_at','desc')->paginate(20);
         //var_dump($comments->user->id);
        //var_dump($comments);
         if($request->ajax()){
            $view = (String) view('admin.dashboard.post.comments_row',compact('posts'));
            return response()->json(['html'=>$view]);
        }else{
         return view('admin.dashboard.post.comments',compact('comments'));
        }
     }

     // Desable Comment From ADMIN Panel

     public function desableComment(Request $request,$post_id,$comment_id){


            $comment=Comment::where(['id'=>$comment_id,'post_id'=>$post_id,'is_active'=>1])->first();
            if($comment){
                DB::table('notifications')
                ->where('data','like','%"event":"COMMENTED_ON_ARTICLE"%')
                ->where('data','like','%"comment_id":'.$comment->id.'%')
                ->delete();
                $comment->is_active=0;
                $comment->save();
                Comment::where(['parent_id'=>$comment_id,'post_id'=>$post_id])->update(['is_active'=>0]);
                CommentLike::where(['comment_id'=>$comment->id])->update(['is_active'=>0]);
                $replies=Comment::where(['parent_id'=>$comment_id,'post_id'=>$post_id])->get();
                foreach($replies as $reply){
                    CommentLike::where('comment_id',$reply->id)->update(['is_active' => 0]);
                }
                $total_comments=Comment::where(['post_id'=>$post_id,'is_active'=>1])->count();
                
                if($request->ajax()){
                return response()->json(array('status'=>'success','message'=>'Comment Desabled','total_comments'=>$total_comments));
               }else{
                return redirect()->back();
                }
              }
            else{
                return response()->json(array('status'=>'error','message'=>'comment not found'));
            }
        
    }

    // Desable Comment From ADMIN Panel

     public function enableComment(Request $request,$post_id,$comment_id){


            $comment=Comment::where(['id'=>$comment_id,'post_id'=>$post_id,'is_active'=>0])->first();
            if($comment){
                DB::table('notifications')
                ->where('data','like','%"event":"COMMENTED_ON_ARTICLE"%')
                ->where('data','like','%"comment_id":'.$comment->id.'%')
                ->delete();
                $comment->is_active=1;
                $comment->save();
                Comment::where(['parent_id'=>$comment_id,'post_id'=>$post_id])->update(['is_active'=>1]);
                CommentLike::where(['comment_id'=>$comment->id])->update(['is_active'=>1]);
                $replies=Comment::where(['parent_id'=>$comment_id,'post_id'=>$post_id])->get();
                foreach($replies as $reply){
                    CommentLike::where('comment_id',$reply->id)->update(['is_active' => 1]);
                }
                $total_comments=Comment::where(['post_id'=>$post_id,'is_active'=>1])->count();
                if($request->ajax()){
                return response()->json(array('status'=>'success','message'=>'Comment Enabled','total_comments'=>$total_comments));
               }else{
                return redirect()->back();
                }
              }
            else{
                return response()->json(array('status'=>'error','message'=>'comment not found'));
            }
        
    }

         public function deleteComment(Request $request,$post_id,$comment_id){


            $comment=Comment::where(['id'=>$comment_id,'post_id'=>$post_id])->first();
            if($comment){
                DB::table('notifications')
                ->where('data','like','%"event":"COMMENTED_ON_ARTICLE"%')
                ->where('data','like','%"comment_id":'.$comment->id.'%')
                ->delete();
                DB::table('comments')->where('id','=',$comment->id)->delete();
                Comment::where(['parent_id'=>$comment_id,'post_id'=>$post_id])->delete();
                CommentLike::where(['comment_id'=>$comment->id])->delete();
                $replies=Comment::where(['parent_id'=>$comment_id,'post_id'=>$post_id])->get();
                foreach($replies as $reply){
                    CommentLike::where('comment_id',$reply->id)->delete();
                }
                $total_comments=Comment::where(['post_id'=>$post_id,'is_active'=>1])->count();
                if($request->ajax()){
                return response()->json(array('status'=>'success','message'=>'comment deleted','total_comments'=>$total_comments));
               }else{
                return redirect()->back();
                }
             }
            else{
                return response()->json(array('status'=>'error','message'=>'comment not found'));
            }
        
    }

      // function for showing a blog post by post id
    public function showBlogPost(Request $request,$id)
    {
        $post=Post::where('id',$id)->with('user','categories','keywords')->first();
        $fakeLikes=DB::table('fake_likes')->where('post_id',$post->id)->count();
        $article_status=ArticleStatus::where('post_id',$post->id)->first();
        $monetisation=Monetisation::where('post_id',$post->id)->first();
        //var_dump($article_status);
        if($post){
            $threads=PostThreads::where('post_id',$post->id)->get();
               for($i=0;$i<count($threads);$i++){
                 $detailThread=Thread::find($threads[$i]->thread_id);
                 $threads[$i]['thread_name']=$detailThread->name;
             }
            if($request->has('json') && $request->query('json')==1){
                $response['post']=$post;
                $response['threads']=$threads;
                return response()->json($response);
            }
            return view('admin.dashboard.post.show',compact('post','threads','fakeLikes','article_status','monetisation'));

        }else{
            return view('admin.error.404');
        }
    }

    public function showPostLikes(Request $request,$id){
        $post=Post::where('id',$id)->first();
        if($post){
            $date_wise_count=DB::table("likes")
                        ->where(['post_id'=>$post->id,'is_active'=>1])
                        ->select(DB::raw("DATE(liked_at) as date"),
                                DB::raw("(COUNT(*)) as total"))
                        ->orderBy(DB::raw("DATE(liked_at),MONTH(liked_at),YEAR(liked_at)"))
                        ->groupBy(DB::raw("DATE(liked_at),MONTH(liked_at),YEAR(liked_at)"))
                        ->get();
            $count['active_likes']=Like::where(['post_id'=>$post->id,'is_active'=>1])->count();
            $count['disabled_likes']=Like::where(['post_id'=>$post->id,'is_active'=>0])->count();
            $likes=Like::where('post_id',$post->id)->with('user')->orderBy('liked_at','desc')->paginate(100);
            if($request->has('json') && $request->query('json')==1){
                $response['likes']=$likes;
                $response['count']=$count;
                $response['date_wise_count']=$date_wise_count;
                return response()->json($response);
            }
            return view('admin.dashboard.post.likes',compact('post','likes','count','date_wise_count'));
        }else{
            return view('admin.error.404');
        }
    }

    public function deletePostLikes(Request $request,$id){
        $post=Post::where('id',$id)->first();
        if($post){
         $deleted=Like::where('id',$request->input('deleteid'))->delete();
         if($deleted){
             $post->likes=$post->likes-1;
             $post->save();
             return redirect()->back();
         }
        }else{
            return view('admin.error.404');
        }
    }

    public function updatePostLikes(Request $request,$id){
        $post=Post::where('id',$id)->first();
        $update_ids=explode(',',$request->input('updateid'));
        if($post){
            for($i=0;$i<count($update_ids);$i++){
                $like=Like::where('id',$update_ids[$i])->first();
                $like->is_active=$like->is_active==0?1:0;
                $like->save();
            }
            $post->likes=Like::where(['post_id'=>$post->id,'is_active'=>1])->count();
            $post->save();
            return redirect()->back();
        }else{
            return view('admin.error.404');
        }
    }

     // function for showing write new post form
     public function showWritePostForm(){
        return view('admin.dashboard.post.write');
    }

    // function for create new post
    public function createPost(Request $request){

    }

    // function for showing edit post form by post id
    public function showEditPostForm(Request $request,$id){
        $post=Post::where('id',$id)->with('user')->first();
        $categories=DB::table('category_posts')
        ->where('category_posts.post_id',$post->id)
        ->select('category_posts.category_id')
        ->get();
        $post->categoryids=$categories->pluck('category_id')->toArray();
        $threads=DB::table('post_threads')
        ->join('threads','post_threads.thread_id','=','threads.id')
        ->where('post_threads.post_id',$post->id)
        ->select('threads.name')
        ->get();
        $post->threads = implode(',',$threads->pluck('name')->toArray());
        if($post){
             if($request->has('json') && $request->query('json')==1){
                $response['post']=$post;
                return response()->json($response);
             }
             return view('admin.dashboard.post.edit',compact('post'));
        }else{
            return view('admin.error.404');
        }
    }

      // function fot update post
      public function updatePost(Request $request){

        $this->validate($request,[
            'title'=>'required',
            'categories'=>'required',
        ]);

        $post=Post::where('id',$request->input('post_id'))->first();
        $old_plag_check=$post->plagiarism_checked;
        $req_plag_check=$request->input('plagiarism_checked')!==null?$request->input('plagiarism_checked'):'0';
        $req_is_plag=$request->input('is_plagiarized')!==null?$request->input('is_plagiarized'):'0';
        $status=$request->input('status')!==null?$request->input('status'):'0';

        $uniqueid=$post->uuid;
        $newPost=$this->save_post($post,
                        $request->input('title'),
                        $uniqueid,
                        $request->input('coverimage'),
                        $request->input('body'),
                        $request->input('plainbody'),
                        $request->input('user_id'),
                        $status,
                        $request->input('categories'),
                        $req_plag_check,
                        $req_is_plag,
                        $request->input('plagiarism_percentage')
                        );

        $opinionThreads=[];
        $slugtemp = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $newPost->title)));
        $posts  = Post::whereRaw("slug REGEXP '^{$slugtemp}([0-9]*)?$'")->get();
        $count = count($posts);
        if($count > 1){
            $slug = $slugtemp.$count;
            }
        else{
            $slug = $slugtemp;
        }
        if($request->has('hidden-tags')){
            $opinionThreads=$this->create_threads($newPost,$request->input('categories'),$request->input('hidden-tags'));
        }else{
            DB::table('post_threads')->where('post_id', '=', $newPost->id)->delete();
        }

        if($request->has('hidden-keywords') && strlen($request->input('hidden-keywords'))>0){
            $postKeywords=$this->create_keywords($post,$request->input('hidden-keywords'));
        }else{
            DB::table('post_keywords')->where('post_id', '=', $newPost->id)->delete();
        }

        $opinion=ShortOpinion::where('post_id',$newPost->id)->first();
        if($opinion){
            $this->create_opinion_from_post($request->getSchemeAndHttpHost(),$newPost, $request->input('user_id'),$opinion,$opinion->uuid,$opinionThreads,$slug,'update');
        }else{
            $opinion=new ShortOpinion();
            $this->create_opinion_from_post($request->getSchemeAndHttpHost(),$newPost, $request->input('user_id'),$opinion,uniqid(),$opinionThreads,$slug,'create');
        }
        DB::table('posts')->where('id', '=', $post->id)->update(['slug' => $slug]);
        if($status=='0'){
            $this->isActiveModeChanger($newPost->id,$status,'draft');
            return redirect()->route('admin.edit_post',['id'=>$post->id])->with(['status'=>'draft','statusText'=>'Opinion has been saved as Draft']);
        }else{
            $this->isActiveModeChanger($newPost->id,$status,'published');

            /* CONDITION FOR CHECKING AND SENDING PLAGIARISM MAIL TO USER
            if($newPost->is_active==1 && $old_plag_check!=$req_plag_check && $req_plag_check==1 && $req_is_plag==1){
                $user=User::where('id',$newPost->user_id)->first();
                $this->send_email_for_plagiarism($newPost,$user);
            }
            */

            return redirect()->route('admin.edit_post',['id'=>$post->id])->with(['status'=>'published','statusText'=>'Opinion has been successfully updated']);
        }
    }

    // function for activate / deactivate post
    public function manageVisibilityPost(Request $request){

        $post_id=$request->input('post_id');
        $is_active=$request->input('is_active')=='0'?1:0;
        $this->isActiveModeChanger($post_id,$is_active,'activate');
        return redirect()->back();
    }


    // function for permanent delete post
    public function deletePost(Request $request){
        $post=Post::where('id',$request->input('post_id'))->first();
        if($post)
        {
            DB::transaction(function () use($post){

                DB::table('category_posts')->where('post_id', '=', $post->id)->delete();
                DB::table('post_threads')->where('post_id', '=', $post->id)->delete();
                DB::table('post_keywords')->where('post_id', '=', $post->id)->delete();
                DB::table('shares')->where('post_id', '=', $post->id)->delete();
                DB::table('likes')->where('post_id', '=', $post->id)->delete();
                DB::table('fake_likes')->where('post_id', '=', $post->id)->delete();
                DB::table('views')->where('post_id', '=', $post->id)->delete();
                DB::table('monetisation')->where('post_id', '=', $post->id)->delete();
                DB::table('bookmarks')->where('post_id', '=', $post->id)->delete();
                $comments=DB::table('comments')->where('post_id', '=', $post->id)->get();
                foreach($comments as $comment){
                     DB::table('comments_likes')->where('comment_id',$comment->id)->delete();
                }
                DB::table('comments')->where('post_id', '=', $post->id)->delete();
                DB::table('category_posts')->where('post_id', '=', $post->id)->delete();
                DB::table('post_threads')->where('post_id', '=', $post->id)->delete();
                DB::table('report_posts')->where('post_id', '=', $post->id)->delete();


                 DB::table('notifications')
                 ->where('data','like','%"event":"ARTICLE_PUBLISHED"%')
                 ->orWhere('data','like','%"event":"COMMENTED_ON_ARTICLE"%')
                 ->orWhere('data','like','%"event":"ARTICLE_LIKED"%')
                 ->where('data','like','%"post_id":'.$post->id.'%')
                 ->delete();

                 $opinion=DB::table('short_opinions')->where('post_id', '=', $post->id)->first();
                 if($opinion){
                    DB::table('thread_opinions')->where('short_opinion_id',$opinion->id)->delete();
                    DB::table('short_opinion_comments')->where('short_opinion_id',$opinion->id)->delete();
                    DB::table('short_opinion_likes')->where('short_opinion_id',$opinion->id)->delete();
                    DB::table('notifications')
                    ->where('data','like','%"event":"OPINION_LIKED"%')
                    ->orWhere('data','like','%"event":"COMMENTED_ON_OPINION"%')
                    ->where('data','like','%"opinion_id":'.$opinion->id.'%')
                    ->delete();

                    DB::table('short_opinions')->where('post_id', '=', $post->id)->delete();
                }
                 DB::table('posts')->where('id', '=', $post->id)->delete();
                });
                 return redirect()->route('admin.posts');
        }
        else{
            return redirect()->route('admin.posts');
        }
    }

    public function reportIssues(Request $request){
        $issues=ReportPost::with('user','post')->where('mark_read',0)->orderBy('created_at','desc')->paginate(50);
        $closed_issues=ReportPost::with('user','post')->where('mark_read',1)->orderBy('created_at','desc')->paginate(50);
        if($request->has('json') && $request->query('json')==1){
            return response()->json($issues);
        }
        return view('admin.dashboard.post.issues',compact('issues','closed_issues'));
     }

     public function closeReportedIssues(Request $request){
         $id=$request->input('id');
         $update=ReportPost::where('id',$id)->update(['mark_read'=>1]);
         if($request->ajax()){
             if($update){return response()->json(array('status'=>'success','message'=>'Issue Closed'));}
             else{return response()->json(array('status'=>'error','message'=>'Failed To Close Issue'));}
         }else{
             if($update){return redirect()->back()->with(array('status'=>'success','message'=>'Issue Closed'));}
             else{return redirect()->back()->with(array('status'=>'error','message'=>'Failed To Close Issue'));}
         }
     }

     public function deleteReportedIssues(Request $request){
         $deleted=ReportPost::where('id',$request->input('id'))->delete();
         if($request->ajax()){
             if($deleted){return response()->json(array('status'=>'success','message'=>'Issue Deleted'));}
             else{return response()->json(array('status'=>'error','message'=>'Failed To Delete Issue'));}
         }else{
             if($deleted){return redirect()->back()->with(array('status'=>'success','message'=>'Issue Deleted'));}
             else{return redirect()->back()->with(array('status'=>'error','message'=>'Failed To Delete Issue'));}
         }
     }

     public function deleteAllReportedIssues(Request $request){
         $deleted=DB::table('report_posts')->delete();
         if($request->ajax()){
             if($deleted){return response()->json(array('status'=>'success','message'=>'All Issues Deleted'));}
             else{return response()->json(array('status'=>'error','message'=>'Failed To Delete Issues'));}
         }else{
             if($deleted){return redirect()->back()->with(array('status'=>'success','message'=>'All Issues Deleted'));}
             else{return redirect()->back()->with(array('status'=>'error','message'=>'Failed To Delete Issues'));}
         }
     }

     public function deleteOfferEligiblePosts(Request $request){
        $deleted=OfferPost::where('id',$request->input('offerpost_id'))->delete();
        if($deleted){
            return response()->json(array('status'=>'success','message'=>'OFFERPOST DELETED SUCCESSFULLY'));
        }else{
            return response()->json(array('status'=>'error','message'=>'FAILED TO DELETED SUCCESSFULLY'));
        }
    }

     public function showOfferEligiblePosts(Request $request){
         $offerposts=OfferPost::where('is_active',1)->with('post','user')->orderBy('created_at','desc')->paginate(50);
         if($request->has('json') && $request->query('json')==1){
            return response()->json($offerposts);
        }
         return view('admin.dashboard.post.offerposts',compact('offerposts'));
     }

     public function sendPaymentMailToUser(Request $request){

        $post=Post::where('id',$request->input('post_id'))->first();
        $user=User::where('id',$request->input('user_id'))->first();
        $sent=$this->send_email_for_payment($user);
        if($sent){
            OfferPost::where(['post_id'=>$post->id,'user_id'=>$user->id])->update(['payment_status'=>1]);
            if($request->ajax()){
                return response()->json(array('status'=>'success','message'=>'Email Successfully Sent'));
            }else{
                return redirect()->back();
            }
        }else{
            if($request->ajax()){
                return response()->json(array('status'=>'error','message'=>'Failed To Sent Email'));
            }else{
                return redirect()->back();
            }
        }
     }


     public function add_fake_likes(Request $request){
        $post_id=$request->input('post_id');
        $add_fake=$request->input('add_fake');

        $pre_likes=Like::where('post_id',$post_id)->with('user')->get();
        $pre_liked_userids=$pre_likes->pluck('user_id')->toArray();
        array_push($pre_liked_userids,'33','48','50','51','52','53');
        $pre_liked_userids=array_unique($pre_liked_userids);

        $not_liked_users=User::where('is_active',1)->whereNotIn('id',$pre_liked_userids)->pluck('id')->toArray();

        $not_liked_users=array_rand($not_liked_users,count( $not_liked_users));

        $added_count=0;
        for($i=0;$i<$add_fake;$i++){
            $foundInDB=Like::where(['user_id'=>$not_liked_users[$i],'post_id'=>$post_id])->exists();
            if($foundInDB){}else{
                Like::create(['user_id'=>$not_liked_users[$i],'post_id'=>$post_id]);
                DB::table('fake_likes')->insert(['user_id'=>$not_liked_users[$i],'post_id'=>$post_id]);
                $added_count++;
            }
        }

        $post=Post::where('id',$post_id)->first();
        $update_likes_number=(int)$post->likes + (int)$added_count;
        Post::where('id',$post_id)->update(['likes'=>$update_likes_number]);
        return redirect()->back();
    }

    public function remove_fake_likes(Request $request){
        $post_id=$request->input('post_id');
        $remove_fake=$request->input('remove_fake');
        $post=Post::where('id',$post_id)->first();

        $fake_likes=DB::table('fake_likes')->where('post_id',$post_id)->take($remove_fake)->get();
        for($i=0;$i<$remove_fake;$i++){
            DB::table('likes')->where(['post_id'=>$fake_likes[$i]->post_id,'user_id'=>$fake_likes[$i]->user_id])->delete();
        }
        $updated_likes=$post->likes - count($fake_likes);
        DB::table('posts')->where('id',$post_id)->update(['likes'=>$updated_likes]);

        for($i=0;$i<$remove_fake;$i++){
        DB::table('fake_likes')->where(['post_id'=>$fake_likes[$i]->post_id,'user_id'=>$fake_likes[$i]->user_id])->delete();
        }
        return redirect()->back();
    }

    public function remove_all_fake_likes(Request $request){
        $post_id=$request->input('post_id');
        $fake_likes=DB::table('fake_likes')->where('post_id',$post_id)->get();
        for($i=0;$i<count($fake_likes);$i++){
            DB::table('likes')->where('post_id',$fake_likes[$i]->post_id)->where('user_id',$fake_likes[$i]->user_id)->delete();
        }
        $post=DB::table('posts')->where('id',$post_id)->first();
        $updated_likes=$post->likes - count($fake_likes);
        DB::table('posts')->where('id',$post_id)->update(['likes'=>$updated_likes]);
        DB::table('fake_likes')->where('post_id',$post_id)->delete();
        return redirect()->back();
    }

    public function check_for_eligibility(Request $request){
        
        $post_id=$request->input('post_id');
        $user_id=$request->input('user_id');
        $promo_review=$request->input('promo_review');
        $backlink=$request->input('backlink');
        $post=Post::where('id',$post_id)->with('user','categories')->first();
        $user= User::where('id',$user_id)->first();
        $article_status=new ArticleStatus();
        $article_status->post_id=$post_id;
        $article_status->user_id=$user_id;
        $article_status->promo_review=$promo_review;
        $article_status->backlink=$backlink;
        $article_status->save();
        if($promo_review == '0' || $backlink == '0'){
            try{
            //Mail::send(new AccountCreatedMail($user,$user->verify_token));
            $mailJET=new MailJetHelper();
            $mailJET->send_post_reject_mail($user,$post);
        }
        catch(\Exception $e){}
        }
        if($request->ajax()){
            return response()->json(array('status'=>'success','promo_review'=>$promo_review,'backlink'=>$backlink,'message'=>'ELigibility Updated Succesfully'));
        }else{
            return redirect('/');
        }
    }


    public function check_for_plagiarismv3(Request $request){

        Requests::register_autoloader();
        $headers_login = array(
            'Content-type' => 'application/json'
        );
        $data_login = '{
          "email": "'.config('plagiarism.plagiarism_mail').'",
          "key": "'.config('plagiarism.plagiarism_key').'"
        }';
        $login_response = Requests::post('https://id.copyleaks.com/v3/account/login/api', $headers_login, $data_login);
        $login_token =  json_decode($login_response->body);
        $authrization = "Bearer ".$login_token->access_token;
       // var_dump($authrization);

        $headers = array(
            'Authorization' => $authrization,
            'Content-type' => 'application/json'
        );
        $data = '{
          "url": "https://weopined.com/opinion/why-suicide-is-not-a-solution",
          "properties": {
            "webhooks": {
              "status": "https://weopined.com/webhook/{STATUS}/123"
            }
          }
        }';
        $response = Requests::put('https://api.copyleaks.com/v3/businesses/submit/url/123', $headers, $data);
        var_dump($response);
    }
    public function check_for_plagiarism(Request $request ,$id){
        $post=Post::where('id',$id)->with('user','categories')->first();
        $article_status=ArticleStatus::where('post_id',$post->id)->first();
       
        $email = config('plagiarism.plagiarism_mail');
        $apiKey = config('plagiarism.plagiarism_key');
        $config = new \ReflectionClass('Copyleaks\Config');
        $clConst = $config->getConstants();
        try{
            $clCloud = new CopyleaksCloud($email, $apiKey, "businesses");
            }catch(Exception $e){
                echo $e->getMessage();
                die();
            }

        //validate login token
        if(!isset($clCloud->loginToken) || !$clCloud->loginToken->validate()){
            echo "<Br/><strong>Bad login credentials</strong>";
            die();
        }
        
        try{
            
            // Create process using one of the following option.
            //$process  = $clCloud->createByURL("https://weopined.com/opinion/open-up-your-heart-and-the-sky-is-yours", $additionalHeaders);
             $process  = $clCloud->createByText($post->plainbody);

            // Wait for the scan to complete
            while ($process->getStatus() != 100)
            {
                sleep(2);              
            }

            $results = $process->getResult();
            // Print the results
            if($results){
            $plagiarism_avg=[];
            foreach ($results as $result) {
                $plagiarism_list = $result->Percents;
                array_push($plagiarism_avg,$plagiarism_list);
                $article_plagiarism=new ArticlePlagiarism();
                $article_plagiarism->post_id=$post->id;
                $article_plagiarism->process_id=$process->processId;
                $article_plagiarism->title=$result->Title;
                $article_plagiarism->introduction=$result->Introduction;
                $article_plagiarism->url=$result->URL;
                $article_plagiarism->plagiarised_words=$result->NumberOfCopiedWords;
                $article_plagiarism->plagiarism_percents=$result->Percents;
                $article_plagiarism->embeded_comparison=$result->EmbededComparison;
                $article_plagiarism->cached_version=$result->CachedVersion;
                $article_plagiarism->comparison_report=$result->ComparisonReport;
                $article_plagiarism->save();
                 
            }
            $plagiarism_avg = array_filter($plagiarism_avg);
                    if(count($plagiarism_avg)) {
                        $plagiarism_average = array_sum($plagiarism_avg)/count($plagiarism_avg);
                        if($plagiarism_average>60){
                            $color_code = "#ff1100";
                        }elseif ($plagiarism_average>30 && $plagiarism_average<60) {
                            $color_code = "#d45252";
                        }
                        else{
                            $color_code = "#8452d4";
                        }
                    }
                
                DB::table('article_status')->where('post_id',$post->id)->update(['plagiarism_tested' => 1]);
                }
            else{
                $article_plagiarism=new ArticlePlagiarism();
                $article_plagiarism->post_id=$post->id;
                $article_plagiarism->process_id="Not Found";
                $article_plagiarism->title="Not Found";
                $article_plagiarism->introduction="Not Found";
                $article_plagiarism->url="Not Founds";
                $article_plagiarism->plagiarised_words=0;
                $article_plagiarism->plagiarism_percents=0;
                $article_plagiarism->embeded_comparison="Not Founds";
                $article_plagiarism->cached_version="Not Founds";
                $article_plagiarism->comparison_report="Not Founds";
                $article_plagiarism->save();
                $plagiarism_average = 0;
                $color_code = "#8452d4";
                DB::table('article_status')->where('post_id',$post->id)->update(['plagiarism_tested' => 1]);
                
            }
             }catch(Exception $e){

                    echo $e->getMessage();
                }

            
            if($request->ajax()){
            return response()->json(array('result' =>$results,'process'=>$process,'post'=>$post));
            }else{
                 return view('admin.dashboard.post.plagiarism',compact('results','process','post','plagiarism_average','color_code','article_status'));
            }
    }

    public function view_plagiarism(Request $request ,$id){
    $plagiarism_article = ArticlePlagiarism::where('post_id',$id)->get();
    $post=Post::where('id',$id)->with('user','categories')->first();
    $article_status=ArticleStatus::where('post_id',$post->id)->first();
       
        if($plagiarism_article){
            $plagiarism_avg=[];
            foreach ($plagiarism_article as $plagiarism_artcl){
                $plagiarism_list = $plagiarism_artcl->plagiarism_percents;
                array_push($plagiarism_avg,$plagiarism_list);
            }
            $plagiarism_avg = array_filter($plagiarism_avg);
                    if(count($plagiarism_avg)) {
                        $plagiarism_average = array_sum($plagiarism_avg)/count($plagiarism_avg);
                        if($plagiarism_average>60){
                            $color_code = "#ff1100";
                        }elseif ($plagiarism_average>30 && $plagiarism_average<60) {
                            $color_code = "#d45252";
                        }
                        else{
                            $color_code = "#8452d4";
                        }
                    }
                else{
                    $plagiarism_average = 0;
                    $color_code = "#8452d4";
                }
              if($request->ajax()){
                return response()->json(array('plagiarism_article' =>$plagiarism_article,'post'=>$post,'plagiarism_average'=>$plagiarism_average,'color_code'=>$color_code));
                }else{
                     return view('admin.dashboard.post.plagiarism_tested',compact('plagiarism_article','post','plagiarism_average','color_code','article_status'));
                }
        }
    }

    public function plagiarised_checked(Request $request, $id, $plagiarised, $plagiarism_average){

        DB::table('article_status')->where('post_id',$id)->update(['plagiarised' => $plagiarised,'plagiarism_percent'=>$plagiarism_average]);
        DB::table('posts')->where('id',$id)->update(['plagiarism_checked' => 1]);
        $post=Post::where('id',$id)->with('user','categories')->first();
        $user= User::where('id',$post->user_id)->first();
        if($plagiarised=='1'){
        
        try{
            //Mail::send(new AccountCreatedMail($user,$user->verify_token));
            $mailJET=new MailJetHelper();
            $mailJET->send_post_plagiarism_mail($user,$post);
        }
        catch(\Exception $e){}
        }
        elseif($plagiarised=='0'){
        
        try{
            //Mail::send(new AccountCreatedMail($user,$user->verify_token));
            $mailJET=new MailJetHelper();
            $mailJET->send_post_selected_mail($user,$post);
        }
        catch(\Exception $e){}
        }
        return redirect()->route('admin.blog_post',['id'=>$id]);
    }
    

    public function manageMonetisation(Request $request){
        $check_for_monetisation = Monetisation::where('post_id',$request->input('post_id'))->first();
        $monetisation_count = Monetisation::where('user_id',$request->input('user_id'))->count();
        
        if($check_for_monetisation){
            $updated=DB::table('monetisation')->where('post_id', '=', $request->input('post_id'))->update(['is_monetised' => $request->input('monetise')]);
            DB::table('posts')->where('id', '=', $request->input('post_id'))->update(['is_monetised' => $request->input('monetise')]);
            if($updated){
                return response()->json(array('status'=>'success','message'=>'Monetisation Updated','status'=>$request->input('monetise')));
            }else{
                return response()->json(array('status'=>'error','message'=>'Failed To Update Monetisation'));
            }
        }
        else{

                $monetisation=new Monetisation();
                $monetisation->post_id=$request->input('post_id');
                $monetisation->user_id=$request->input('user_id');
                $monetisation->is_monetised=$request->input('monetise');
                $monetisation->save();
                DB::table('posts')->where('id', '=', $request->input('post_id'))->update(['is_monetised' => $request->input('monetise')]);
                if($monetisation_count<4){
                    $check_offer=UserEarning::where(['user_id'=>$request->input('user_id')])->first();
                    if($check_offer){
                        $sum_earning = $check_offer->total_earning + 5;
                        UserEarning::where(['user_id'=>$request->input('user_id')])->update(['total_earning'=>$sum_earning]);
                        $check_offer_post = RsmUserPost::where(['post_id'=>$request->input('post_id')])->first();
                        if($check_offer_post){
                            $sum_earning_post = $check_offer_post->money + 5;
                            RsmUserPost::where(['post_id'=>$request->input('post_id')])->update(['money'=>$sum_earning_post]);
                        }
                        else{
                            $offer_added_post=new RsmUserPost();
                            $offer_added_post->post_id=$request->input('post_id');
                            $offer_added_post->user_id=$request->input('user_id');
                            $offer_added_post->money=5;
                            $offer_added_post->save();
                        }
                    }
                    else{
                        $offer_added=new UserEarning();
                        $offer_added->user_id=$request->input('user_id');
                        $offer_added->total_earning=5;
                        $offer_added->save();
                        $check_offer_post = RsmUserPost::where(['post_id'=>$request->input('post_id')])->first();
                        if($check_offer_post){
                            $sum_earning_post = $check_offer_post->money + 5;
                            RsmUserPost::where(['post_id'=>$request->input('post_id')])->update(['money'=>$sum_earning_post]);
                        }
                        else{
                            $offer_added_post=new RsmUserPost();
                            $offer_added_post->post_id=$request->input('post_id');
                            $offer_added_post->user_id=$request->input('user_id');
                            $offer_added_post->money=5;
                            $offer_added_post->save();
                            }
                        }
                    }
            return response()->json(array('status'=>'success','message'=>'Monetisation Updated','status'=>$request->input('monetise')));
        }
    }
    /*-------------helper functions------------------*/


    protected function save_post($post,$title,$uniqueid,$coverimage,$body,$plain,$user_id,$status,$categories,$plagiarism_checked,$is_plagiarized,$plagiarism_percentage)
    {
        $totalLength=strlen($plain);
        $readtime=1+round($totalLength/375);
        $slug =$this->create_slug($title);

        $post->title=$title;
        $post->slug=$slug;
        $post->uuid=$uniqueid;
        $post->coverimage=$coverimage;
        $post->body=$body;
        $post->plainbody=$plain;
        $post->user_id=$user_id;
        $post->status=$status;
        $post->readtime=$readtime;
        $post->plagiarism_checked=$plagiarism_checked;
        $post->is_plagiarized=$is_plagiarized;
        $post->plagiarism_percentage=$plagiarism_percentage;
        $post->save();
        $post->categories()->sync($categories);
        return $post;
    }


     protected function create_opinion_from_post($host,$post,$user_id,$opinion,$uniqueid,$opinionThreads,$slug,$event)
     {
         $body='';
         foreach($opinionThreads as $thread){
            $body=$body.' <a href="/thread/'.$thread->name.'" class="thread_link">#'.$thread->name.'</a>';
         }

        $links=array();
        $info['status']='OK';
        $info['title']=$post->title;
        $info['description']=str::limit($post->plainbody,40,'...');
        $info['url']=$host.'/opinion/'.$slug;
        $info['type']='link';
        $info['image']=$post->coverimage;
        $info['providerName']='Opined';
        $info['providerUrl']='https://www.weopined.com';
        $info['providerIcon']='https://www.weopined.com/favicon.png';
        array_push($links,$info);

        $opinion->uuid=$uniqueid;
        $opinion->body=$body;
        $opinion->cover=NULL;
        $opinion->cover_type='none';
        $opinion->links=json_encode($links);
        $opinion->user_id=$user_id;
        $opinion->type="post";
        $opinion->post_id=$post->id;
        $opinion->is_active=1;
        $opinion->save();

        if(count($opinionThreads)>0){
            $threadIds=Arr::pluck($opinionThreads, 'id');
            $opinion->threads()->sync($threadIds);
        }

     }

     protected function create_threads($post,$categories,$threads)
     {
        $opinionThreads=[];

        $threads=explode(' ',$threads);
        if(count($threads)>0){
            for($i=0;$i<count($threads);$i++){
                $threads[$i]=ltrim($threads[$i],'#');
                if(strlen($threads[$i])>2){
                    $threadSlug=str::slug($threads[$i]);
                    $threadFound=Thread::whereRaw('LOWER(`name`) = ?',[trim(strtolower($threads[$i]))])->first();
                    if($threadFound){
                        array_push($opinionThreads,$threadFound);
                        $ThreadId=$threadFound->id;
                        $threadFound->categories()->syncWithoutDetaching($categories);
                    }else{
                        $threadCreate=Thread::create(['name'=>$threads[$i],'slug'=>$threadSlug]);
                        $ThreadId=$threadCreate->id;
                        $thread=Thread::find($ThreadId);
                        $thread->categories()->sync($categories);
                        array_push($opinionThreads,$threadCreate);
                    }
                }
            }
            $threadIds=Arr::pluck($opinionThreads, 'id');
            $post->threads()->sync($threadIds);
            return $opinionThreads;
        }
     }

     protected function create_keywords($post,$keywords){
        $postKeywords=[];
        $keywords=explode(',',$keywords);
        if(count($keywords)>0){
            for($i=0;$i<count($keywords);$i++){
                $keywords[$i]=ltrim($keywords[$i],'#');
                $keywordSlug=str::slug($keywords[$i]);
                $keywordFound=Keyword::whereRaw('LOWER(`name`) = ?',[trim(strtolower($keywords[$i]))])->first();
                if($keywordFound){
                    array_push($postKeywords,$keywordFound);
                    $KeywordId=$keywordFound->id;
                }else{
                    $keywordCreate=Keyword::create(['name'=>$keywords[$i],'slug'=>$keywordSlug]);
                    $KeywordId=$keywordCreate->id;
                    array_push($postKeywords,$keywordCreate);
                }
            }
            $keywordIds=Arr::pluck($postKeywords, 'id');
            $post->keywords()->sync($keywordIds);
            return $postKeywords;
        }
    }

     protected function isActiveModeChanger($post_id,$is_active,$event){
        $post=Post::where('id',$post_id)->first();
        if($post){
            DB::transaction(function () use($post,$is_active,$event){

                DB::table('category_posts')->where('post_id', '=', $post->id)->update(['is_active' => $is_active]);
                DB::table('post_threads')->where('post_id', '=', $post->id)->update(['is_active' => $is_active]);
                DB::table('post_keywords')->where('post_id', '=', $post->id)->update(['is_active' =>$is_active]);
                DB::table('shares')->where('post_id', '=', $post->id)->update(['is_active' => $is_active]);
                DB::table('likes')->where('post_id', '=', $post->id)->update(['is_active' => $is_active]);
                DB::table('views')->where('post_id', '=', $post->id)->update(['is_active' => $is_active]);
                DB::table('bookmarks')->where('post_id', '=', $post->id)->update(['is_active' => $is_active]);
                DB::table('comments')->where('post_id', '=', $post->id)->update(['is_active' => $is_active]);
                DB::table('monetisation')->where('post_id', '=', $post->id)->update(['is_monetised' => $is_active]);
                DB::table('posts')->where('id', '=', $post->id)->update(['is_monetised' => $is_active]);
                $comments=DB::table('comments')->where('post_id', '=', $post->id)->get();
                foreach($comments as $comment){
                     DB::table('comments_likes')->where('comment_id',$comment->id)->update(['is_active' => $is_active]);
                }
                DB::table('report_posts')->where('post_id', '=', $post->id)->update(['is_active' => $is_active]);

                DB::table('notifications')
                 ->where('data','like','%"event":"ARTICLE_PUBLISHED"%')
                 ->orWhere('data','like','%"event":"COMMENTED_ON_ARTICLE"%')
                 ->orWhere('data','like','%"event":"ARTICLE_LIKED"%')
                 ->where('data','like','%"post_id":'.$post->id.'%')
                 ->update(['is_active' => $is_active]);

                 $opinion=ShortOpinion::where('post_id', '=', $post->id)->first();
                 if($opinion){
                    $opinion->is_active = $is_active;
                    $opinion->save();
                    DB::table('thread_opinions')->where('short_opinion_id',$opinion->id)->update(['is_active'=>$is_active]);
                    DB::table('short_opinion_comments')->where('short_opinion_id',$opinion->id)->update(['is_active'=>$is_active]);
                    $opinion_comments=DB::table('short_opinion_comments')->where('short_opinion_id',$opinion->id)->get();
                    foreach($opinion_comments as $op_comment){
                        DB::table('short_opinion_comments_likes')->where('comment_id',$op_comment->id)->update(['is_active'=>$is_active]);
                    }
                    DB::table('short_opinion_likes')->where('short_opinion_id',$opinion->id)->update(['is_active'=>$is_active]);
                    DB::table('shares')->where('short_opinion_id',$opinion->id)->update(['is_active'=>$is_active]);
                    DB::table('notifications')
                    ->where('data','like','%"event":"OPINION_LIKED"%')
                    ->orWhere('data','like','%"event":"COMMENTED_ON_OPINION"%')
                    ->where('data','like','%"opinion_id":'.$opinion->id.'%')
                    ->update(['is_active'=>$is_active]);
                   }
                if($event=='activate'){
                    DB::table('posts')->where('id', '=', $post->id)->update(['is_active' => $is_active]);
                }
            });
        }
    }

    /* protected function send_email_for_plagiarism($post,$user){
        try{  Mail::send(new PlagiarismMail($user,$post));}
        catch(\Exception $e){}
    } */

    protected function send_email_for_payment($user){
        try{  Mail::send(new PaymentMail($user));}
        catch(\Exception $e){}
        return count(Mail::failures())>0?false:true;
    }

    public function uploadPostImage(Request $request,$id){
        $inputName='coverimage';
        $folder='cover';
        $validation = Validator::make($request->all(),
        [
            'coverimage'=>'required|mimes:jpeg,jpg,png,gif|max:10000'
        ]);

        if ($validation->fails()){
            return redirect()->route('admin.edit_post',['id'=>$id]);
        }

        if($request->hasFile($inputName)){
            $uniqueid=uniqid();
            $original_name=$request->file($inputName)->getClientOriginalName();

            $size=$request->file($inputName)->getSize();
            $extension=$request->file($inputName)->getClientOriginalExtension();
            $name=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
            $imagepath='/storage/'.$folder.'/'.$name;
            $path=$request->file($inputName)->storeAs('public/'.$folder,$name);
            Post::where('id',$id)->update(['coverimage'=>$imagepath]);
            return redirect()->route('admin.edit_post',['id'=>$id]);
        }
    }


}
