<?php

namespace App\Jobs\Resize;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Image;

class ResizeImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $sourcePath,$destinationPath,$sizes;

    public function __construct($sourcePath,$destinationPath,$sizes)
    {
       $this->sourcePath= $sourcePath;
       $this->destinationPath=$destinationPath;
       $this->sizes=$sizes;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
        $sourcePath=$this->sourcePath;
        $destinationPath=$this->destinationPath;
        $sizes=$this->sizes;

        foreach($sizes as $size){
            $filename=pathinfo($sourcePath, PATHINFO_FILENAME);
            $extension= pathinfo($sourcePath, PATHINFO_EXTENSION);
            $imagename = $filename.'_'.$size[0].'x'.$size[1].'.'.$extension;
            Image::make($sourcePath)->resize($size[0],$size[1])->save($destinationPath.'/'.$imagename);
        }

        }catch(\Exception $e){

        }
    }
}
