<?php

namespace App\Http\Controllers\Api\Community;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Community;
use App\Model\FileManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use ImageOptimizer;
use App\Jobs\Resize\ResizeImageJob;
use Illuminate\Contracts\Bus\Dispatcher;
use DB;

class CommunityCrudController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:api',['except'=>['etFun']]);
    }

    public function etFun(){

    }
    public function index(Request $request){
        
        

        $community_ids = DB::table('community_members')->where(['user_id'=>Auth::user()->user_id,'is_active'=>1])->pluck('community_id')->toArray();

        
        $communities = Community::where(['is_active'=>1])->whereIn('id',$community_ids)->orWhere(['user_id'=>Auth::user()->user_id])->get();


        $formatted_community=[];
            foreach($communities as $community){
                $formatted=$this->formatted_community($community);
                array_push($formatted_community,$formatted);
            }

        $response=array('status'=>'success','result'=>1,'community'=>$formatted_community);
        return response()->json($response, 200);
    }

    public function update(Request $request){
    
        try {
            //code...
            $validator = Validator::make($request->all(), [
                'id'=>'required',
                'name'=>'required',
                'description'=>'required',
                'contest_category'=>'required',
            ]);

            if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
            $data = $request->all();
            $community = Community::where(['id'=>$data['id'],'is_active'=>1])->first();
            $cover = $community->cover_image;

            if($request->hasFile('cover')){
            $file = $request->file('cover');
            $uniqueid=uniqid();
            $original_name=$file->getClientOriginalName();
            $size=$file->getSize();
            $extension=$file->getClientOriginalExtension();
            if($size>5048576){
                $response=array('status'=>'error','result'=>0,'errors'=>'Image is larger than 5 MB');
                return response()->json($response, 200);
            }else if(!in_array(strtolower($extension),["jpg","jpeg","png","svg","gif"])){
                $response=array('status'=>'error','result'=>0,'errors'=>'Image format is invalid');
                return response()->json($response, 200);
            }else{
                $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                $imagepath=url('/storage/opinion/'.$filename);
                $path=$file->storeAs('public/opinion',$filename);
                $size=$this->optimize_image($extension,'opinion',$filename,$size);
                $this->save_file_to_db($uniqueid,$imagepath,$filename,$original_name,'OPINION_COVER_IMAGE',$size,$extension,Auth::user()->user_id);
                $cover=$imagepath;
                try{
                    $job = (new ResizeImageJob(storage_path('app/public/opinion/'.$filename),storage_path('app/public/opinion/'),[[314,240]]))->onQueue('default');
                    app(Dispatcher::class)->dispatch($job);
                }catch(\Exception $e){}
            }
        }
           
           $new_community = $this->save_community($community,$request->all(),uniqid(),$cover);
                $this->remove_null($community);

                $response=array('status'=>'succcess','result'=>1, 'community'=>$new_community);
                return response()->json($response, 200);

        }
        
        } catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error'.$e);
            return response()->json($response, 500);
        }
    }

    public function my_communities(Request $request){
        
        $communities = Community::where(['is_active'=>1,'user_id'=>Auth::user()->user_id])->get();


        $formatted_community=[];
            foreach($communities as $community){
                $formatted=$this->formatted_community($community);
                array_push($formatted_community,$formatted);
            }

        $response=array('status'=>'success','result'=>1,'community'=>$formatted_community);
        return response()->json($response, 200);
    }

    public function store(Request $request){
       
        try{
            $validator = Validator::make($request->all(), [
                'name'=>'required',
                'description'=>'required',
                'contest_category'=>'required',
            ]);

            if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
            $file = $request->file('cover');
            $uniqueid=uniqid();
            $original_name=$file->getClientOriginalName();
            $size=$file->getSize();
            $extension=$file->getClientOriginalExtension();
            if($size>5048576){
                $response=array('status'=>'error','result'=>0,'errors'=>'Image is larger than 5 MB');
                return response()->json($response, 200);
            }else if(!in_array(strtolower($extension),["jpg","jpeg","png","svg","gif"])){
                $response=array('status'=>'error','result'=>0,'errors'=>'Image format is invalid');
                return response()->json($response, 200);
            }else{
                $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                $imagepath=url('/storage/opinion/'.$filename);
                $path=$file->storeAs('public/opinion',$filename);
                $size=$this->optimize_image($extension,'opinion',$filename,$size);
                $this->save_file_to_db($uniqueid,$imagepath,$filename,$original_name,'OPINION_COVER_IMAGE',$size,$extension,Auth::user()->user_id);
                $cover=$imagepath;
                try{
                    $job = (new ResizeImageJob(storage_path('app/public/opinion/'.$filename),storage_path('app/public/opinion/'),[[314,240]]))->onQueue('default');
                    app(Dispatcher::class)->dispatch($job);
                }catch(\Exception $e){}
            }

                $community = $this->save_community(new Community(),$request->all(),uniqid(),$cover);
                $this->remove_null($community);
                

                $response=array('status'=>'success','result'=>1,'message'=>'Community Created','community'=>$community);
                return response()->json($response,200);
           }
        }
        catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    protected function save_community(Community $community,array $data,$unique_id,$cover){

        $community->name=$data['name'];
        $community->uuid=$unique_id;
        $community->cover_image=$cover;
        $community->description=isset($data['description'])?$data['description']:null;
        $community->user_id=Auth::user()->user_id;
        $community->contest_category = $data['contest_category'];
        $community->is_active= 1;
        $community->save();
       
        return $community;
    }


}