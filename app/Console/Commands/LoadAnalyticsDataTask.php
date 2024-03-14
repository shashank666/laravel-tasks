<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\RedirectResponse;
use Spatie\Analytics\Period;
use Spatie\Analytics\AnalyticsFacade;
use Illuminate\Support\Str;
use App\Model\AnalyticsData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class LoadAnalyticsDataTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LoadData:Daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Task for getting data from Google Analytics';

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
        
        $result=file_get_contents("https://www.weopined.com/data");
    
     
    }
    
}

