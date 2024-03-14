<?php

namespace App\Jobs\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Support\Facades\Storage;
use ImageOptimizer;

class CompressImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $file;
    public $tries = 3;
    public $timeout = 14400; // 4 hours

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file)
    {
        $this->$file=$file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
            if (file_exists(storage_path('app/'.$this->file))){
                $ext = pathinfo($this->file, PATHINFO_EXTENSION);
                if(in_array(strtolower($ext),['jpg','jpeg','png','gif'])){
                    try{
                        ImageOptimizer::optimize(storage_path('app/'.$this->file));
                    }catch(\Exception $e){

                    }
                }
            }
    }
}
