<?php

namespace App\Jobs\Opinion;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use FFMpeg;
use DB;

class TestVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $inputVideoPath,$filemanager_uuid,$user_id;
    public $tries = 1;
    public $timeout = 3000; // 50 minutes
    public $retryAfter = 3600; // 60 minutes

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($inputVideoPath,$filemanager_uuid,$user_id)
    {
        $this->inputVideoPath=$inputVideoPath;
        $this->filemanager_uuid=$filemanager_uuid;
        $this->user_id=$user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $inputVideoPath=$this->inputVideoPath;
        $filemanager_uuid=$this->filemanager_uuid;
        $user_id=$this->user_id;

        $filename=pathinfo($inputVideoPath, PATHINFO_FILENAME);
        $extension= pathinfo($inputVideoPath, PATHINFO_EXTENSION);
        $new_filename=$filename.'_'.$user_id.'.'.$extension;

       // try{
            /* FFMpeg::open($inputVideoPath)
            ->export()
            ->toDisk('local')
            ->inFormat(new \FFMpeg\Format\Video\X264('aac', 'libx264'))
            ->save('public/videos/'.$new_filename); */

            FFMpeg::open($inputVideoPath)->save(new FFMpeg\Format\Video\X264('aac', 'libx264'),storage_path('app/public/videos/'.$filename.'_'.$user_id.'.'.$extension));

            $getID3 = new \getID3;
            $file1 = $getID3->analyze($inputVideoPath);
            $duration1 =  (int)$file1['playtime_seconds'];

            $file2 = $getID3->analyze(storage_path('app/public/videos'.$new_filename));
            $duration2 =  (int)$file2['playtime_seconds'];

            if($duration1==$duration2){
                throw new \Exception('Video Completed');
            }

            //FFMpeg::open($inputVideoPath)->save(new FFMpeg\Format\Video\X264('aac', 'libx264'),storage_path('app/public/videos/'.$filename.'_'.$user_id.'.'.$extension));
           /*  $video = FFMpeg::open($inputVideoPath);
            $webm_format = new FFMpeg\Format\Video\WebM();
            $video->export()->inFormat($webm_format)->save(storage_path('app/public/videos/'.$filename.'_'.$user_id.'.'.'webm'));
 */
            //Storage::delete('public/videos/'.$filename.'.'.$extension);
            //Storage::move('public/videos/'.$filename.'_'.$user_id.'.'.$extension,'public/videos/'.$filename.'.'.$extension);
            //$updated_size=Storage::size('public/videos/'.$filename.'.'.$extension);
            //DB::table('file_manager')->where(['user_id'=>$user_id,'unique_id'=>$filemanager_uuid,'event'=>'OPINION_COVER_VIDEO'])->update(['size'=>$updated_size]);
        /*  }catch(\Exception $e){

        } */
    }

    public function failed()
    {

    }
}
