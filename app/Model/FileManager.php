<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use App\Model\User;
use App\Model\Post;
use App\Model\ShortOpinion;
use App\Model\Comment;
use App\Model\Category;

class FileManager extends Model
{
    protected $table='file_manager';
    public $primaryKey='id';
    public $timestamps=true;
    protected $fillable = [
        'unique_id', 'path','name','original_name','event','size','extension','user_id','post_id','is_active'
    ];
    protected $appends = ['formatted_size','file_in_use'];


    public function getFormattedSizeAttribute(){
        return $this->filesize_formatted($this->size);
    }

    public function getFileInUseAttribute(){
        return $this->file_in_use();
    }

    public function uploaded_by(){
        return $this->belongsTo('App\Model\User','user_id')->select('id','name','image','username','unique_id','is_active');
    }

    public function file_in_use(){
        $event=$this->event;
        if($event=='USER_PROFILE'){
           return User::where('image',$this->path)->exists();
        }else if($event=='CATEGORY_IMAGE'){
            return Category::where('image',$this->path)->exists();
        }else if($event=='POST_COVER'){
            return Post::where('coverimage',$this->path)->exists();
        }else if($event=='POST_COMMENT'){
            return Comment::where('media',$this->path)->exists();
        }else if($event=='OPINION_COMMENT'){
            return ShortOpinionComment::where('media',$this->path)->exists();
        }else if($event=='OPINION_COVER_VIDEO' || $event=='OPINION_COVER_IMAGE'){
            return ShortOpinion::where('cover',$this->path)->exists();
        }else if($event=='OPINION_COVER_VIDEO_THUMBNAIL'){
            return ShortOpinion::where('thumbnail',$this->path)->exists();
        }else{
            return true;
        }
    }

    public function filesize_formatted($size)
    {
    $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }
}
