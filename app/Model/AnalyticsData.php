<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;

class AnalyticsData extends Model
{
    protected $table='adsensedata';
    public $primaryKey='ID';
	
	protected $fillable = [
		'pagePath',
		'dateHourMinute',
		'adsenseRevenue',
		'adsenseAdUnitsViewed',
		'adsenseAdsViewed',
		'adsenseAdsClicks',
		'adsensePageImpressions',
		'adsenseCTR',
		'adsenseECPM',
		'adsenseExits',
		'adsenseViewableImpressionPercent',
		'adsenseCoverage',
	];
}
