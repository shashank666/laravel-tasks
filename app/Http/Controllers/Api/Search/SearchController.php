<?php

namespace App\Http\Controllers\Api\Search;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Model\User;
use App\Model\Category;
use App\Model\Thread;
use App\Model\Post;
use App\Model\ShortOpinion;

use DB;
use Config;


class SearchController extends Controller
{

    public function __construct()
    {

    }

    public function search(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'query'=>'required',
            ]);

           if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
            $query=$request->query('query');

            $posts=Post::select('id','title','slug','uuid','coverimage','created_at')->where(['status'=>1,'is_active'=>1])->where('title', 'LIKE', '%'.$query.'%')->orderby('created_at','desc')->take(50)->get();
            $categories=Category::select('id','name','image')->where(['is_active'=>1])->where('name', 'LIKE', '%'.$query.'%')->take(50)->get();
            $threads=Thread::select('id','name')->where(['is_active'=>1])->where('name', 'LIKE', '%'.$query.'%')->take(100)->get();
            $users=User::select('id','name','username','unique_id','image')->where(['is_active'=>1])->where('name', 'LIKE', '%'.$query.'%')->take(100)->get();


            $user_id=-1;
            if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                $following_ids=User::find($user_id)->active_followings->pluck('id')->toArray();
            }else{
                $following_ids=[];
            }

            $formatted_users=collect($users)->map(function($user,$key) use($following_ids){
                return $this->formatted_user($following_ids,$user);
            });

            $response=array('status'=>'success',
                            'result'=>1,
                            'posts'=>$posts,
                            'categories'=>$categories,
                            'threads'=>$threads,
                            'users'=>$formatted_users);
            $this->remove_null($response);
            return response()->json($response,200);
           }
         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }


    public function search_thread(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'query'=>'required',
            ]);

           if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
                $query=$request->query('query');
                $threads=Thread::select('id','name')->where(['is_active'=>1])->where('name', 'LIKE', '%'.$query.'%')->take(100)->get();
                if(!empty($threads) && count($threads)>0){
                    $response=array('status'=>'success','result'=>1,'threads'=>$threads);
                    $this->remove_null($response);
                    return response()->json($response, 200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'No results');
                    return response()->json($response, 200);
                }
           }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function search_category(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'query'=>'required',
            ]);

           if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
                $query=$request->query('query');
                $categories=Category::select('id','name','image')->where(['is_active'=>1])->where('name', 'LIKE', '%'.$query.'%')->get();
                if(!empty($categories) && count($categories)>0){
                    $response=array('status'=>'success','result'=>1,'categories'=>$categories);
                    $this->remove_null($response);
                    return response()->json($response, 200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'No results');
                    return response()->json($response, 200);
                }
           }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function search_post(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'query'=>'required',
            ]);

           if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
                $query=$request->query('query');
                $posts=Post::select('id','title','slug','uuid','coverimage','created_at')->where(['status'=>1,'is_active'=>1])->where('title', 'LIKE', '%'.$query.'%')->take(50)->get();
                if(!empty($posts) && count($posts)>0){
                    $response=array('status'=>'success','result'=>1,'posts'=>$posts);
                    $this->remove_null($response);
                    return response()->json($response, 200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'No results');
                    return response()->json($response, 200);
                }
           }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function search_user(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'query'=>'required',
            ]);

           if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
                $query=$request->query('query');
                $users=User::select('id','name','username','unique_id','image')->where(['is_active'=>1])->where('name', 'LIKE', '%'.$query.'%')->take(50)->get();
                if(!empty($users) && count($users)>0){

                    $user_id=-1;
                    if($request->header('Authorization')){
                        $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                        $following_ids=User::find($user_id)->active_followings->pluck('id')->toArray();
                    }else{
                        $following_ids=[];
                    }

                    $formatted_users=collect($users)->map(function($user,$key) use($following_ids){
                        return $this->formatted_user($following_ids,$user);
                    });

                    $response=array('status'=>'success','result'=>1,'users'=>$formatted_users);
                    $this->remove_null($response);
                    return response()->json($response, 200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'No results');
                    return response()->json($response, 200);
                }
           }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function search_bank(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'query'=>'required',
            ]);

           if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{

                $banks=DB::table('banks')->where('BANK','LIKE',strtoupper($request->query('query')).'%')->distinct('BANK')->orderBy('BANK','asc')->take(100)->get(['BANK']);

                if($banks && count($banks)>0){
                    $banks=array_unique($banks->pluck('BANK')->toArray());
                    $response=array('status'=>'success','result'=>1,'banks'=> $banks);
                    $this->remove_null($response);
                    return response()->json($response,200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'No results');
                    return response()->json($response,200);
                }
           }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function search_city(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'query'=>'required',
            ]);

           if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
            $cities=DB::table('banks')->where('CITY','LIKE',strtoupper($request->query('query')).'%')->orderBy('CITY','asc')->distinct('CITY')->take(100)->get(['CITY']);
            if($cities && count($cities)>0){
                $cities=array_unique($cities->pluck('CITY')->toArray());
                $response=array('status'=>'success','result'=>1,'cities'=> $cities);
                $this->remove_null($response);
                return response()->json($response,200);
            }else{
                $response=array('status'=>'error','result'=>0,'errors'=>'No results');
                return response()->json($response,200);
            }
           }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

}
