<?php

namespace App\Http\Controllers\Frontend\FileManager;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Model\User;
use Image;

use DB;
use Carbon\Carbon;

 class FileManagerController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function upload(Request $request,String $event){

        if($event=='CATEGORY_IMAGE'){
            $inputName='categoryimage';
            $folder='category';
            $validation = Validator::make($request->all(),
            [
                'categoryimage'=>'required|mimes:jpeg,jpg,png,gif|max:2048'
            ]);
        }

        if($event=='POST_COVER' || $event=='BLOG_POST'){
            $inputName='coverimage';
            $folder='cover';
            $validation = Validator::make($request->all(),
            [
                'coverimage'=>'required|mimes:jpeg,jpg,png,gif|max:2048'
            ]);
        }

        if($event=='USER_PROFILE'){
            $inputName='profileimage';
            $folder='profile';
            $validation = Validator::make($request->all(),
            [
                'profileimage'=>'required|mimes:jpeg,jpg,png,gif|max:2048'
            ]);
        }

        if($event=='USER_COVER'){
            $inputName='cover_image';
            $folder='cover_image';
            $validation = Validator::make($request->all(),
            [
                'cover_image'=>'required|mimes:jpeg,jpg,png,gif|max:2048'
            ]);
        }

        if($event=='POST_COMMENT' || $event=='OPINION_COMMENT'){
            $inputName='commentimage';
            $folder='comments';
            $validation = Validator::make($request->all(),
            [
                'commentimage'=>'required|mimes:jpeg,jpg,png,gif|max:2048'
            ]);
        }

        if($event=='VIDEO'){
            $inputName='video';
            $folder='videos';
            $validation = Validator::make($request->all(),
            [
                'video'=>'required|mimes:mp4,webm|max:500000'
            ]);
        }


        if ($validation->fails()){
            $response=array('status'=>'error','errors'=>$validation->errors()->toArray());
            return response()->json($response);
        }



        if($request->hasFile($inputName)){



            $uniqueid=uniqid();
            $original_name=$request->file($inputName)->getClientOriginalName();

            $original_size=$request->file($inputName)->getSize();
            $extension=$request->file($inputName)->getClientOriginalExtension();
            if($original_name=="blob" || $original_name==1 || $original_name=="1"){
                $extension='jpg';
            }
            $name=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;

             $photo = $request->file($inputName);
            //$imagename = time().'.'.$photo->getClientOriginalExtension();
            $destinationPath = public_path('/storage/'.$folder);

            //Profile Picture
            if($folder=="profile"){
            $imagename = Carbon::now()->format('Ymd').'_'.$uniqueid.'_100x100'.'.'.$photo->getClientOriginalExtension();
            $thumb_img_dp = Image::make($photo->getRealPath())->resize(100, 100);
            $thumb_img_dp->save($destinationPath.'/'.$imagename);

            $imagename = Carbon::now()->format('Ymd').'_'.$uniqueid.'_40x40'.'.'.$photo->getClientOriginalExtension();
            $thumb_img_dp_small = Image::make($photo->getRealPath())->resize(40, 40);
            $thumb_img_dp_small->save($destinationPath.'/'.$imagename);
            }

            //Profile Cover
            if($folder=="cover_image"){
            $imagename = Carbon::now()->format('Ymd').'_'.$uniqueid.'_760x200'.'.'.$photo->getClientOriginalExtension();
            $thumb_img_cover = Image::make($photo->getRealPath())->resize(760, 200);
            $thumb_img_cover->save($destinationPath.'/'.$imagename);
            }

            //Profile Opinion-Cover
            if($folder=="cover"){
            
            if($photo->getClientOriginalExtension()!=null){
                $ext = $photo->getClientOriginalExtension();
            }
            else{
                $ext = 'jpg';
            }
            //Profile article-Cover
            $imagename = Carbon::now()->format('Ymd').'_'.$uniqueid.'_350x250'.'.'.$ext;
            $thumb_img_profile_article = Image::make($photo->getRealPath())->resize(350, 250);
            $thumb_img_profile_article->save($destinationPath.'/'.$imagename);

            //Threadpage card

            $imagename = Carbon::now()->format('Ymd').'_'.$uniqueid.'_314x240'.'.'.$ext;
            $thumb_img_profile_article = Image::make($photo->getRealPath())->resize(314, 240);
            $thumb_img_profile_article->save($destinationPath.'/'.$imagename);

            }

            $imagepath=url('/storage/'.$folder.'/'.$name);
            $path=$request->file($inputName)->storeAs('public/'.$folder,$name);
            if($path){
                $size=$this->optimize_image($extension,$folder,$name,$original_size);
                $user_id=Auth::check()?auth()->user()->id:0;
                $uploadedFile=$this->save_file_to_db($uniqueid,$imagepath,$name,$original_name,$event,$size,$extension,$user_id);
                return response()->json(array('status'=>'success','message'=>'Image successfully uploaded','image'=>$uploadedFile->path));
            }else{
                return response()->json(array('status'=>'error','message'=>'failed to upload image'));
            }
        }
    }

    public function upload_by_url(Request $request,$event){
        $url=$request->input('url');
        $contents = file_get_contents($url);
        $uniqueid=uniqid();

        if($event=='POST_COVER' || $event=='BLOG_POST'){
            $folder='cover';
            $validation = Validator::make($request->all(),
            [
                'url'=>'required|url'
            ]);

            if ($validation->fails()){
                $response=array('status'=>'error','errors'=>$validation->errors()->toArray());
                return response()->json($response);
            }

            if($contents){
                $extension= 'jpg';// pathinfo($url, PATHINFO_EXTENSION) ||
                $name=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                Storage::put('public/'.$folder.'/'.$name,$contents);

                $original_size = Storage::size('public/'.$folder.'/'.$name);
                $imagepath=url('/storage/'.$folder.'/'.$name);
                $size=$this->optimize_image($extension,$folder,$name,$original_size);
                if(Storage::exists('public/'.$folder.'/'.$name)){
                    $user_id=Auth::check()?auth()->user()->id:0;
                    $uploadedFile=$this->save_file_to_db($uniqueid,$imagepath,$name,$url,$event,$size,$extension,$user_id);
                    return response()->json(array('status'=>'success','message'=>'Image successfully uploaded','image'=>$uploadedFile->path));
                }else{
                    return response()->json(array('status'=>'error','message'=>'failed to upload image'));
                }
            }else{
                return response()->json(array('status'=>'error','message'=>'failed to load image'));
            }
        }

    }

    public function serve_user_image(Request $request,$user_id){
        $type=$request->has('type') && $request->query('type')!=null?$request->query('type'):'profile';
        $size=$request->has('size') && $request->query('size')!=null && in_array($request->query('size'),['thumb','full'])?$request->query('size'):'thumb';

        $user=User::select('id','image','cover_image')->where('id',$user_id)->first();
        if($user){
                if($type=='profile'){
                    $filename=pathinfo($user->image, PATHINFO_FILENAME);
                    $extension= pathinfo($user->image, PATHINFO_EXTENSION);
                    $resize=$size=='thumb'?$filename.'_100x100.'.$extension:$filename.'.'.$extension;
                    $path = Storage::exists('public/profile/'.$resize)? storage_path('app/public/profile/' . $resize): storage_path('app/public/profile/' . $filename.'.'.$extension);
                    $path = $user->image!=null ? $path:public_path('/img/avatar_thumb.png');

                }else{
                    $filename=pathinfo($user->cover_image, PATHINFO_FILENAME);
                    $extension= pathinfo($user->cover_image, PATHINFO_EXTENSION);
                    $resize=$size=='thumb'?$filename.'_760x200.'.$extension:$filename.'.'.$extension;
                    $path = Storage::exists('public/cover_image/'.$resize)? storage_path('app/public/cover_image/' . $resize): storage_path('app/public/cover_image/' . $filename.'.'.$extension);
                    $path = $user->cover_image!=null ?$path:storage_path('app/public/cover_image/cover.png');
                }

            try{
                return Image::make($path)->response();
            }catch(\Exception $e){
                 if($type=='profile'){ return Image::make(public_path('/img/avatar_thumb.png'))->response();}
                 else{ return Image::make(storage_path('app/public/cover_image/cover.png'))->response();};
            }
        }else{
            abort(404);
        }
    }

}
