<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class MoveUnacknowledgedAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:move-unacknowledged';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move unacknowledged current alerts older than 24 hours to the alerts table.';

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
     * @return int
     */
    public function handle()
    {
        $twentyFourHoursAgo = Carbon::now()->subHours(24);
        $alerts = DB::table('DeviceLinesAlertCurrent')->where('AckDateTime', '0000-00-00 00:00:00')->get();
        foreach ($alerts as $alert) {
            $alertTimeDate = Carbon::parse($alert->AlertDateTime);
            $currentDate = Carbon::now();
            $formattedTime = date('Y-m-d H:i:s');
            $name = Session::get('userID');
            $alertCurrent = DB::table('DeviceLinesAlertCurrent')->where('DevicesID', $alert->DevicesID)->where('Line', $alert->Line)->first();
            if ($alertCurrent) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                DB::table('DeviceLinesAlert')->insert([
                    'DeviceLinesAlertStatus' => $alert->DeviceLinesAlertStatus,
                    'AccountID' => $alert->AccountID,
                    'DevicesID' => $alert->DevicesID,
                    'UserAccountID' => 1,
                    'AlertID' => $alert->AlertID,
                    'Line' => $alert->Line,
                    'DeviceStatus' => $alert->DeviceStatus,
                    'BeerBrandID' => $alert->BeerBrandID,
                    'KegTypeID' => $alert->KegTypeID,
                    'DistributorID' => $alert->DistributorID,
                    'OptTemp' => $alert->OptTemp,
                    'AlertCNT' => $alert->AlertCNT,
                    'OptPressure' => $alert->OptPressure,
                    'TempPressAlert' => $alert->TempPressAlert,
                    'TempPressAlertTimeOut' => $alert->TempPressAlertTimeOut,
                    'TempAlertValue' => $alert->TempAlertValue,
                    'PressAlertValue' => $alert->PressAlertValue,
                    'KegCost' => $alert->KegCost,
                    'LineLength' => $alert->LineLength,
                    'LineType' => $alert->LineType,
                    'AlertDateTime' => $alert->AlertDateTime,
                    'AckDateTime' => $formattedTime,
                    'min_value' => $alertCurrent->min_value,
                    'max_value' => $alertCurrent->max_value,
                    'RecordStatus' => $alert->RecordStatus,
                    'InsertDateTime' => $alert->InsertDateTime,
                    'UpdateDateTime' => $alert->UpdateDateTime,

                ]);
                DB::table('DeviceLinesAlertCurrent')->where('DeviceLinesAlertCurrentID', $alert->DeviceLinesAlertCurrentID)->delete();
            }
        }
        Log::channel('custom_log')->info('Unacknowledged current alerts older than 24 hours moved to alerts table.');
    }
}
