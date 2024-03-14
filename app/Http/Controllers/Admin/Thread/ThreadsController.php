<?php

namespace App\Http\Controllers\Admin\Thread;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use App\Model\Category;
use App\Model\Thread;
use App\Model\PostThreads;
use App\Model\ThreadLike;
use App\Model\ThreadFollower;
use App\Model\ThreadOpinion;
use App\Model\CategoryThread;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionLike;
use App\Model\ShortOpinionComment;
use App\Model\ShortOpinionCommentLike;
use App\Model\PollThread;
use App\Model\User;


use DB;
use Carbon\Carbon;

class ThreadsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        View::share('menu','threads');
    }

    public function showThreads(Request $request){

        $count['active']=DB::table('threads')->where('is_active',1)->count();
        $count['disabled']=DB::table('threads')->where('is_active',0)->count();
        $count['total']=DB::table('threads')->count();

        $from=$request->has('from')?$request->query('from'):Carbon::create(2018, 1, 01, 0, 0, 0);
        $to=$request->has('to')?$request->query('to'):Carbon::now()->endOfDay();

        $is_active=$request->has('is_active')?$request->query('is_active'):'0,1';
        $sortBy=$request->has('sortBy')?$request->query('sortBy'):'id';
        $sortOrder=$request->has('sortOrder')?$request->query('sortOrder'):'desc';

        $searchBy=$request->has('searchBy')?$request->input('searchBy'):'name';
        $searchQuery=$request->has('searchQuery') && strlen(trim($request->input('searchQuery')))>0 ? trim($request->input('searchQuery')):'';
        $DBsearchQuery=$searchBy=='id'? $searchQuery:'%'.$searchQuery.'%';

        $limit=$request->has('limit')?$request->query('limit'):24;
        $page=$request->has('page')?$request->query('page'):1;

        $filter_categories=$request->has('categories')?explode(',',trim($request->query('categories'))):[];

        $likes_count=$request->has('likes_count')?$request->query('likes_count'):0;
        $likes_operator=$request->has('likes_operator')?$request->query('likes_operator'):'>=';

        $followers_count=$request->has('followers_count')?$request->query('followers_count'):0;
        $followers_operator=$request->has('followers_operator')?$request->query('followers_operator'):'>=';

        $posts_count=$request->has('posts_count')?$request->query('posts_count'):0;
        $posts_operator=$request->has('posts_operator')?$request->query('posts_operator'):'>=';

        $opinions_count=$request->has('opinions_count')?$request->query('opinions_count'):0;
        $opinions_operator=$request->has('opinions_operator')?$request->query('opinions_operator'):'>=';

        $query = Thread::query();

        if(isset($DBsearchQuery) && !empty($DBsearchQuery) && strlen($DBsearchQuery)>0){
            if($searchBy=='id'){
                $query->where($searchBy, '=', $DBsearchQuery);
            }else{
                $query->where($searchBy, 'LIKE', $DBsearchQuery);
            }
        }

        $query->with('categories');
        $query->withCount(['opinions','posts','followers','likes']);
        $query->whereIn('is_active',explode(',',$is_active));
        $query->whereBetween('created_at',[$from,$to]);

        if($filter_categories && $filter_categories[0]){
            $query->whereHas('categories', function ($q) use ($filter_categories) {
                $q->whereIn('categories.id',$filter_categories);
            });
        }

        if($request->has('opinions_count') && !empty($request->query('opinions_count'))){
            $query->has('opinions',$opinions_operator,$opinions_count);
        }

        if($request->has('posts_count') && !empty($request->query('posts_count'))){
            $query->has('posts',$posts_operator,$posts_count);
        }

        if($request->has('followers_count') && !empty($request->query('followers_count'))){
            $query->has('followers',$followers_operator,$followers_count);
        }

        if($request->has('likes_count') && !empty($request->query('likes_count'))){
            $query->has('likes',$likes_operator,$likes_count);
        }

        $query->orderBy($sortBy,$sortOrder);
        $threads = $query->paginate($limit);

        if($request->ajax()){
            $view = (String) view('admin.dashboard.thread.thread_row',compact('threads'));
            return response()->json(['html'=>$view]);
        }else{
            return view('admin.dashboard.thread.index',compact('count','threads','searchQuery','searchBy','sortOrder','sortBy','is_active','limit','page','from','to','filter_categories',
            'likes_operator','likes_count','followers_operator','followers_count','posts_operator','posts_count','opinions_operator','opinions_count'));
        }
    }

    /* public function showThreadOpinions(Request $request,$id){
        $thread=Thread::find($id);
        if($thread){
            $thread_opinions=ThreadOpinion::where(['thread_id'=>$thread->id])->orderBy('created_at','desc')->paginate(30);
            $count['active']=ThreadOpinion::where(['thread_id'=>$thread->id,'is_active'=>1])->count();
            $count['disabled']=ThreadOpinion::where(['thread_id'=>$thread->id,'is_active'=>0])->count();
            $opinions=[];
            foreach($thread_opinions as $opinion)
            {
              $opinion=ShortOpinion::where(['id'=>$opinion->short_opinion_id])->with('user')->first();

              if($opinion){
                  $opinion->makeVisible('cpanel_body')->toArray();
                  array_push($opinions,$opinion);
                 }
            }
            return view('admin.dashboard.thread.show',compact('thread','thread_opinions','opinions','count'));
        }else{
            return view('admin.error.404');
        }
    } */

    public function showAddThreadForm(){
        $categories=Category::orderBy('name','asc')->get();
        return view('admin.dashboard.thread.create',compact('categories'));
    }

    public function showEditThreadForm($threadid){
        $thread=Thread::find($threadid);
        if($thread){
            $categories=Category::orderBy('name','asc')->get();
            $selectedCategories=$thread->categories->pluck('id')->toArray();
            return view('admin.dashboard.thread.edit',compact('thread','categories','selectedCategories'));
        }else{
            return view('admin.error.404');
        }
    }

    public function createThread(Request $request){
        $this->validate($request,[
            'name'=>'required',
            'imagefile'=>'nullable|max:50000'
        ]);

        if($request->hasFile('imagefile')){
            $imageurl=$this->uploadThreadImage($request->file('imagefile'));
         }else{
            $imageurl=$request->input('imageurl');
         }

        $name=$request->input('name');
        $slug = str::slug($request->input('name'),'-');
        $description=$request->input('description');
        $categories=$request->input('categories');
        $is_active=1;
        $thread=new Thread();
        $this->saveThread($thread,$name,$slug,$description,$imageurl,$is_active,$categories);
        return redirect()->route('admin.threads');
    }

    public function updateThread(Request $request){
        $this->validate($request,[
            'name'=>'required',
            'imagefile'=>'nullable|max:50000'
        ]);
        if($request->hasFile('imagefile')){
            $imageurl=$this->uploadThreadImage($request->file('imagefile'));
        }else{
           $imageurl=$request->input('imageurl');
        }

            $name=$request->input('name');
            $slug = str::slug($request->input('name'),'-');
            $description=$request->input('description');
            $categories=$request->input('categories');
            $is_active=$request->input('is_active')=='on'?1:0;

            $threadid=$request->input('threadid');
            $thread = Thread::find($threadid);
            $this->saveThread($thread,$name,$slug,$description,$imageurl,$is_active,$categories);
            return redirect()->route('admin.threads');
    }


    protected function uploadThreadImage($file){
        $filenameWithExt=$file->getClientOriginalName();
        $filename=pathinfo($filenameWithExt,PATHINFO_FILENAME);
        $extension=$file->getClientOriginalExtension();
        $fileNameToStore=$filename.'_'.time().'.'.$extension;
        $imageurl= url('/storage/thread/'.$fileNameToStore);
        $file->storeAs('public/thread',$fileNameToStore);
        return $imageurl;
    }

    protected function saveThread(Thread $thread,$name,$slug,$description,$imageurl,$is_active,$categories){
        $thread->name=$name;
        $thread->slug= $slug;
        $thread->description=$description;
        $thread->image=$imageurl;
        $thread->is_active=$is_active;
        $thread->save();
        $thread->categories()->sync($categories);
    }

    public function manageVisibilityThread(Request $request){
        DB::transaction(function () use($request){
            $threadid=$request->input('thread_id');
            $is_active=$request->input('is_active')=='0'?1:0;
            ThreadFollower::where('thread_id',$threadid)->update(['is_active'=>$is_active]);
            ThreadLike::where('thread_id',$threadid)->update(['is_active'=>$is_active]);
            CategoryThread::where('thread_id',$threadid)->update(['is_active'=>$is_active]);
            PollThread::where('thread_id',$threadid)->update(['is_active'=>$is_active]);
            PostThreads::where('thread_id',$threadid)->update(['is_active'=>$is_active]);
            DB::table('notifications')
            ->where('data','like','%"event":"THREAD_LIKED"%')
            ->where('data','like','%"thread_id":'.$threadid.'%')
            ->update(['is_active'=>$is_active]);

            $shortOpinions= ThreadOpinion::where('thread_id',$threadid)->get()->pluck('short_opinion_id')->toArray();
            for($i=0;$i<count($shortOpinions);$i++){
                $active_opinion_threads=  ThreadOpinion::where(['short_opinion_id'=>$shortOpinions[$i],'is_active'=>1])->count();
                if($active_opinion_threads<2){
                    ShortOpinion::where('id',$shortOpinions[$i])->update(['is_active'=>$is_active]);
                    ShortOpinionLike::where('short_opinion_id',$shortOpinions[$i])->update(['is_active'=>$is_active]);
                    ShortOpinionComment::where('short_opinion_id',$shortOpinions[$i])->update(['is_active'=>$is_active]);
                    $opinion_comments=ShortOpinionComment::where('short_opinion_id',$shortOpinions[$i])->get();
                    foreach($opinion_comments as $op_comment){
                        ShortOpinionCommentLike::where('comment_id',$op_comment->id)->update(['is_active'=>$is_active]);
                    }

                    DB::table('notifications')
                    ->where('data','like','%"event":"OPINION_LIKED"%')
                    ->where('data','like','%"event":"COMMENTED_ON_OPINION"%')
                    ->where('data','like','%"opinion_id":'.$shortOpinions[$i].'%')
                    ->update(['is_active'=>$is_active]);
                }
            }
            ThreadOpinion::where('thread_id',$threadid)->update(['is_active'=>$is_active]);
            Thread::where('id',$threadid)->update(['is_active'=>$is_active]);
        });
        return redirect()->back();
    }

    public function deleteThread(Request $request){
        DB::transaction(function () use($request){
            $threadid=$request->input('thread_id');
            ThreadFollower::where('thread_id',$threadid)->delete();
            ThreadLike::where('thread_id',$threadid)->delete();
            PollThread::where('thread_id',$threadid)->delete();
            CategoryThread::where('thread_id',$threadid)->delete();
            PostThreads::where('thread_id',$threadid)->delete();
            DB::table('notifications')
                ->where('data','like','%"event":"THREAD_LIKED"%')
                ->where('data','like','%"thread_id":'.$threadid.'%')
                ->delete();
            $shortOpinions= ThreadOpinion::where('thread_id',$threadid)->get()->pluck('short_opinion_id')->toArray();
            for($i=0;$i<count($shortOpinions);$i++){
                ShortOpinion::where('id',$shortOpinions[$i])->delete();
                ShortOpinionLike::where('short_opinion_id',$shortOpinions[$i])->delete();
                $opinion_comments=ShortOpinionComment::where('short_opinion_id',$shortOpinions[$i])->get();
                foreach($opinion_comments as $op_comment){
                    ShortOpinionCommentLike::where('comment_id',$op_comment->id)->delete();
                }
                ShortOpinionComment::where('short_opinion_id',$shortOpinions[$i])->delete();
                ThreadOpinion::where('short_opinion_id',$shortOpinions[$i])->delete();
                DB::table('notifications')
                ->where('data','like','%"event":"OPINION_LIKED"%')
                ->where('data','like','%"event":"COMMENTED_ON_OPINION"%')
                ->where('data','like','%"opinion_id":'.$shortOpinions[$i].'%')
                ->delete();
            }
            ThreadOpinion::where('thread_id',$threadid)->delete();
            Thread::where('id',$threadid)->delete();
        });
        return redirect()->route('admin.threads');
    }

    public function updateOpinionVisibility(Request $request,$id){
        DB::transaction(function () use($request,$id){
            $opinion=DB::table('short_opinions')->where('id', '=',$id)->first();
            $is_active=$opinion->is_active==0?1:0;
            ShortOpinionLike::where('short_opinion_id', '=',$id)->update(['is_active' => $is_active]);
            ShortOpinionComment::where('short_opinion_id', '=',$id)->update(['is_active' => $is_active]);
            $opinion_comments=ShortOpinionComment::where('short_opinion_id',$id)->get();
            foreach($opinion_comments as $op_comment){
                ShortOpinionCommentLike::where('comment_id',$op_comment->id)->update(['is_active' => $is_active]);
            }
            ThreadOpinion::where('short_opinion_id', '=',$id)->update(['is_active' => $is_active]);
            DB::table('shares')
            ->where('short_opinion_id', '=',$id)->update(['is_active' => $is_active]);

            DB::table('notifications')
            ->where('data','like','%"event":"LIKED_OPINION"%')
            ->where('data','like','%"event":"COMMENTED_ON_OPINION"%')
            ->where('data','like','%"opinion_id":'.$id.'%')
            ->update(['is_active'=>$is_active]);
            DB::table('short_opinions')->where('id', '=',$id)->update(['is_active' => $is_active,'updated_at'=>Carbon::now()]);
        });
        $opinion=DB::table('short_opinions')->where('id', '=',$id)->first();
            $is_active=$opinion->is_active==0?1:0;
        //return redirect()->back();
        if($is_active == 0){
             if($request->ajax()){
                    return response()->json(array('status'=>'success','message'=>'Opinion Successfully Updated','active'=>'disable'));
                }else{
                    return redirect('/');
                }
            }
        elseif($is_active == 1){
             if($request->ajax()){
                    return response()->json(array('status'=>'success','message'=>'Opinion Successfully Updated','active'=>'enable'));
                }else{
                    return redirect('/');
                }
            }
        else{
            return redirect('/');
        }
    }

    public function deleteOpinion(Request $request,$id){
        DB::transaction(function () use($request,$id){
            ShortOpinionLike::where('short_opinion_id', '=',$id)->delete();
            $opinion_comments=ShortOpinionComment::where('short_opinion_id',$id)->get();
            foreach($opinion_comments as $op_comment){
                ShortOpinionCommentLike::where('comment_id',$op_comment->id)->delete();
            }
            ShortOpinionComment::where('short_opinion_id', '=',$id)->delete();
            ThreadOpinion::where('short_opinion_id', '=',$id)->delete();
            DB::table('shares')
            ->where('short_opinion_id', '=',$id)->delete();
            DB::table('notifications')
            ->where('data','like','%"event":"OPINION_LIKED"%')
            ->where('data','like','%"event":"COMMENTED_ON_OPINION"%')
            ->where('data','like','%"opinion_id":'.$id.'%')
            ->delete();
            ShortOpinion::where('id', '=',$id)->delete();
        });
        //return redirect()->back();
        if($request->ajax()){
            return response()->json(array('status'=>'success','message'=>'Opinion Successfully Deleted'));
        }else{
            return redirect('/');
        }
    }

}
