<?php

namespace App\Http\Controllers\Admin\Dashboard;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Model\FileManager;
use Carbon\Carbon;

class FileManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        View::share('menu','filemanager');
    }


    public function index(Request $request){

        $directories = Storage::disk('local')->directories('public');
        unset($directories[0], $directories[1]);
        $total_storage=0;
        $total_files=0;
        $all_dirs=[];
        foreach($directories as $dir)
        {
            $dir_size=0;
            $dir_count=0;
            $dir_name=basename($dir);
            foreach(Storage::allFiles($dir) as $file){
                $dir_size += Storage::size($file);
                $dir_count++;
            }
            $total_storage=$total_storage+$dir_size;
            $total_files=$total_files+$dir_count;
            $dir_size =$this->filesize_formatted($dir_size);
            array_push($all_dirs,array('dir_name'=>$dir_name,'dir_size'=>$dir_size,'dir_count'=>$dir_count));
        }

        $total_storage=$this->filesize_formatted($total_storage);

        $from=$request->has('from')?$request->query('from'):Carbon::create(2018, 1, 01, 0, 0, 0);
        $to=$request->has('to')?$request->query('to'):Carbon::now()->endOfDay();
        $sortBy=$request->has('sortBy')?$request->query('sortBy'):'created_at';
        $sortOrder=$request->has('sortOrder')?$request->query('sortOrder'):'desc';
        $limit=$request->has('limit')?$request->query('limit'):24;
        $page=$request->has('page')?$request->query('page'):1;
        $event=$request->has('event')?$request->query('event'):'all';
        $extension=$request->has('extension')?$request->query('extension'):'all';
        $file_in_use=$request->has('file_in_use')?$request->query('file_in_use'):'0,1';
        $query = FileManager::query();

        $query->whereBetween('created_at',[$from,$to]);
        if($event!='all'){
            $query->where('event',$event);
        }

        if($extension!='all'){
            $query->where('extension',$extension);
        }

        $query->with('uploaded_by');
        $query->orderBy($sortBy,$sortOrder);
        $db_entries = $query->paginate($limit);

        if($request->ajax()){
            $view = (String) view('admin.dashboard.filemanager.components.file_row',compact('db_entries'));
            return response()->json(['html'=>$view]);
        }else{
        return view('admin.dashboard.filemanager.index',
        compact('all_dirs','total_storage','total_files','db_entries',
        'from','to','extension','event',
        'sortBy','sortOrder','limit'));
        }
    }


    public function deleteFile(Request $request){
        $fileID=$request->input('file_id');
        $file=FileManager::where('id',$fileID)->first();
        if($file){
            $slashes=explode("/",$file->path);
            $folder=$slashes[count($slashes)-2];
            $fileName=basename($file->path);
            if(Storage::exists('public/'.$folder.'/'.$fileName)){
                Storage::delete('public/'.$folder.'/'.$fileName);
            }
            FileManager::where('path',$file->path)->delete();
        }
        return redirect()->back();
    }

}
