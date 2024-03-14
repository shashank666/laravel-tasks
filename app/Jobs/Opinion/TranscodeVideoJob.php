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

class TranscodeVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $inputVideoPath,$opinion_uuid,$filemanager_uuid,$user_id;
    public $tries = 1;
    //public $timeout = 3000; // 50 minutes
    //public $retryAfter = 3600; // 60 minutes
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($inputVideoPath,$opinion_uuid,$filemanager_uuid,$user_id)
    {
        $this->inputVideoPath=$inputVideoPath;
        $this->filemanager_uuid=$filemanager_uuid;
        $this->opinion_uuid=$opinion_uuid;
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
        $opinion_uuid=$this->opinion_uuid;
        $user_id=$this->user_id;

        $filename=pathinfo($inputVideoPath, PATHINFO_FILENAME);
        $extension= pathinfo($inputVideoPath, PATHINFO_EXTENSION);
        $new_filename=$filename.'_'.$user_id.'.'.$extension;
        try{

            FFMpeg::open($inputVideoPath)->save(new FFMpeg\Format\Video\X264('aac', 'libx264'),storage_path('app/public/videos/'.$new_filename));

           /*  FFMpeg::fromDisk('local')
            ->open($inputVideoPath)
            ->export()
            ->toDisk('local')
            ->inFormat(new \FFMpeg\Format\Video\X264('aac', 'libx264'))
            ->save('public/videos/'.$new_filename); */

            if(Storage::exists('public/videos/'.$new_filename)){
                $updated_size=Storage::size('public/videos/'.$new_filename);
                DB::table('short_opinions')->where(['user_id'=>$user_id,'uuid'=>$opinion_uuid])->update(['cover'=>url('/storage/videos/'.$new_filename)]);
                DB::table('file_manager')->where(['user_id'=>$user_id,'unique_id'=>$filemanager_uuid,'event'=>'OPINION_COVER_VIDEO'])->update(['size'=>$updated_size,'name'=>$new_filename,'path'=>url('/storage/videos/'.$new_filename)]);
                Storage::delete('public/videos/'.$filename.'.'.$extension);
                //Storage::move('public/videos/'.$new_filename,'public/videos/'.$filename.'.'.$extension);
                throw new Exception('Transcode video completed -'.$opinion_uuid);
            }

         }catch(\Exception $e){
            $this->failed();
        }
    }

    public function failed()
    {
        $this->delete();
    }
}
