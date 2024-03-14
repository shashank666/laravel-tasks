<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::before(function (JobProcessing $event) {
            // $event->connectionName
            // $event->job
            // $event->job->payload()
        });

        Queue::after(function (JobProcessed $event) {
            // $event->connectionName
            // $event->job
            // $event->job->payload()
        });

        Queue::failing(function (JobFailed $event) {
            // $event->connectionName
            // $event->job
            // $event->exception
        });

         View::share('key', 'value');
         Schema::defaultStringLength(191);

         $company=DB::table('company')->where('id',1)->first();
         $company_ui_settings=DB::table('company_ui_settings')->where('id',1)->first();
         $company_app_settings=DB::table('company_app_settings')->where('id',1)->first();
         $categories=DB::table('categories')->where('is_active',1)->get();
         $countries=DB::table('countries')->select('name','code','phone_code')->get();
         config(['app.company' => $company]);
         config(['app.company_ui_settings' => $company_ui_settings]);
         config(['app.company_app_settings' => $company_ui_settings]);

         View::share('company',$company);
         View::share('company_ui_settings',$company_ui_settings);
         View::share('company_app_settings',$company_app_settings);
         View::share('categories',$categories);
         View::share('countries',$countries);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
