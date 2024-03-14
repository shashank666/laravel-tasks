<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\Opinion\TranscodeVideoJob;
use App\Model\ShortOpinion;
use App\Model\FileManager;
use Illuminate\Contracts\Bus\Dispatcher;

class TrasncodeVideoTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'video:transcode {opinion_uuid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transcode video opinion';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $opinion_uuid = $this->argument('opinion_uuid');
        $opinion=ShortOpinion::where(['uuid'=>$opinion_uuid])->first();
        $fn=basename($opinion->cover);
        $inputVideoPath='public/videos/'.$fn;

        $FileManager=FileManager::where(['name'=>$fn,'user_id'=>$opinion->user_id])->first();

        $job = (new TranscodeVideoJob($inputVideoPath,$opinion->uuid,$FileManager->unique_id,$opinion->user_id))->onQueue('videos');
        app(Dispatcher::class)->dispatch($job);

    }
}
