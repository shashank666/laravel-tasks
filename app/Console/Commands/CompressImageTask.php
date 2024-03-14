<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Jobs\Admin\CompressImagesJob;

class CompressImageTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compress:images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for Compress all images';

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
        $directories = Storage::disk('local')->directories('public');
        unset($directories[0], $directories[1]);
        foreach($directories as $dir)
        {
            foreach(Storage::allFiles($dir) as $file){
            dispatch((new CompressImagesJob($file))->onQueue('digest'));
            usleep(2000000);
            }
        }

    }
}
