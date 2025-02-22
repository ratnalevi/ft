<?php

namespace App\Console\Commands;

use App\Mail\AbnormalityAlert;
use App\Models\Account;
use App\Models\DeviceLine;
use App\Models\Devices;
use App\Models\Location;
use App\Models\UserAccount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAlerts extends Command
{
    public const ALERT_INTERVAL = 600;
    public const TDS_UPPER_LIMIT = 650;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:abnormalities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to send alerts when there are no data points or when there are abnormalities in Temp, Press, TDS, After Pour Hours';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $currentTime = now();
        $this->info('current time is: ' . $currentTime);
        $nearestWindowStart = $currentTime->copy()->subMinutes(self::ALERT_INTERVAL)->format('Y-m-d H:i:s');
        $nearestWindowEnd = $currentTime->format('Y-m-d H:i:s');
        $this->info('Start time: ' . $nearestWindowStart);
        $this->info('End time: ' . $nearestWindowEnd);

        // get all accounts
        $accounts = Account::where('RecordStatus', '1')->get();
        if (count($accounts) <= 0) {
            Log::infO('No accounts found');
            return;
        }

        Log::info('Accounts found: ' . count($accounts));
        foreach ($accounts as $account) {
            $deviceLocations = Devices::select(DB::raw('DISTINCT(LocationID)'))->where('AccountID', '=', $account->AccountID)->get();
            foreach ($deviceLocations as $deviceLocation) {
                if ($deviceLocation->LocationID != 111) {
                    continue;
                }

                Log::info('Devices Locations found: ' . count($deviceLocations));

                $alertCounts = [];
                $deviceLines = [];
                $locationId = $deviceLocation->LocationID;
                $location = Location::find($locationId);
                $devices = Devices::where('LocationID', '=', $locationId)->get();
                Log::info('Devices found: ' . count($devices));

                $shouldAlert = false;
                foreach ($devices as $device) {
                    $deviceId = $device->DevicesID;
                    $this->info('Device ID: ' . $deviceId);

                    $alertCounts[$deviceId] = $this->getAlerts($locationId, $deviceId, $nearestWindowStart, $nearestWindowEnd);

                    if (count($alertCounts[$deviceId]['temperature']) > 0
                        || count($alertCounts[$deviceId]['tds']) > 0
                        || count($alertCounts[$deviceId]['pressure']) > 0
                        || count($alertCounts[$deviceId]['after_hours']) > 0) {
                        $shouldAlert = true;
                    }

                    $deviceLines[$deviceId] = DeviceLine::select(['DeviceLines.Line', 'b.Brand'])->join('BeerBrands as b', 'DeviceLines.BeerBrandsID', 'b.BeerBrandsID')->where('DevicesID', '=', $deviceId)->get()->toArray();
                }

                if ($shouldAlert) {
                    $adminUserEmails = UserAccount::select(['u.Email', 'l.LocationName', 'l.LocationID'])
                        ->join('User as u', 'u.UserID', '=', 'UserAccount.UserID')
                        ->join('Location as l', 'l.LocationID', '=', 'UserAccount.LocationID')
                        ->where('UserAccount.ConfigurationID', '=', 9999)
                        ->where('UserAccount.IsActive', '=', 1)
                        ->where('UserAccount.LocationID', '=', $locationId)->get()->pluck('Email')->toArray();

                    $adminUserEmails = [];
                    $adminUserEmails[] = 'ratnalevi@gmail.com';
                    $this->info('Sending emails to ' . implode(',', $adminUserEmails));
                    Mail::to($adminUserEmails)
                        ->send(new AbnormalityAlert($location, $alertCounts, $deviceLines, $nearestWindowStart, $nearestWindowEnd));
                    $this->info('Alerts sent for location: ' . $location->LocationName);
                } else {
                    $this->info('No alerts to be sent for location: ' . $location->LocationName);
                }
            }
        }
    }

    public function getAlerts($locationId, $deviceId, $nearestWindowStart, $nearestWindowEnd): array
    {
        $alerts = [];

        $alerts['temperature'] = $this->getTemperatureAlertsCountByDevice($locationId, $deviceId, $nearestWindowStart, $nearestWindowEnd);
        $alerts['tds'] = $this->getTDSAlertsCountByDevice($locationId, $deviceId, $nearestWindowStart, $nearestWindowEnd);
        $alerts['pressure'] = $this->getPressureAlertsCountByDevice($locationId, $deviceId, $nearestWindowStart, $nearestWindowEnd);
        $alerts['after_hours'] = $this->getAfterHoursPourAlertsCountByDevice($locationId, $deviceId, $nearestWindowStart, $nearestWindowEnd);

        return $alerts;
    }

    public function getTemperatureAlertsCountByDevice($locationId, $deviceId, $startTime, $endTime): array
    {
        $query = $this->getBaseQuery($locationId, $deviceId, $startTime, $endTime)
            ->leftJoin('LineDataCache as ld', 'dl.Line', '=', 'ld.DeviceLinesID')
            ->where(function ($query) {
                $query->where('ld.Temp', '>=', DB::raw('dl.OptTemp + dl.TempAlertValue'))
                    ->orWhere('ld.Temp', '<=', DB::raw('dl.OptTemp - dl.TempAlertValue'))
                    ->orWhere('ld.TempMin', '>=', DB::raw('dl.OptTemp + dl.TempAlertValue'))
                    ->orWhere('ld.TempMin', '<=', DB::raw('dl.OptTemp - dl.TempAlertValue'))
                    ->orWhere('ld.TempMax', '>=', DB::raw('dl.OptTemp + dl.TempAlertValue'))
                    ->orWhere('ld.TempMax', '<=', DB::raw('dl.OptTemp - dl.TempAlertValue'));
            });

        return $query->get()->keyBy('Line')->toArray();
    }

    private function getBaseQuery($locationId, $deviceId, $startTime, $endTime)
    {
        return DeviceLine::from('DeviceLines as dl')
            ->select('dl.Line', DB::raw('COUNT(ld.DeviceLinesID) as Occurrences'))
            ->join('Devices as d', 'd.DevicesID', '=', 'dl.DevicesID')
            ->where('d.LocationID', $locationId)
            ->where('d.DevicesID', $deviceId)
            ->where('ld.ReportDateTime', '>=', $startTime)
            ->where('ld.ReportDateTime', '<=', $endTime)
            ->having('Occurrences', '>', 0)
            ->groupBy('dl.Line')
            ->orderBy('dl.Line', 'asc');
    }

    public function getTDSAlertsCountByDevice($locationId, $deviceId, $startTime, $endTime): array
    {
        $query = $this->getBaseQuery($locationId, $deviceId, $startTime, $endTime)
            ->leftJoin('LineDataCache as ld', 'dl.Line', '=', 'ld.DeviceLinesID')
            ->where(function ($query) {
                $query->where('ld.TDS', '>=', self::TDS_UPPER_LIMIT)
                    ->orWhere('ld.TDSMin', '>=', self::TDS_UPPER_LIMIT)
                    ->orWhere('ld.TDSMax', '>=', self::TDS_UPPER_LIMIT);
            });

        return $query->get()->keyBy('Line')->toArray();
    }

    public function getPressureAlertsCountByDevice($locationId, $deviceId, $startTime, $endTime): array
    {
        $query = $this->getBaseQuery($locationId, $deviceId, $startTime, $endTime)
            ->leftJoin('LineDataCache as ld', 'dl.Line', '=', 'ld.DeviceLinesID')
            ->where(function ($query) {
                $query->where('ld.Pres', '>=', DB::raw('dl.OptPressure + dl.PressAlertValue'))
                    ->orWhere('ld.Pres', '<=', DB::raw('dl.OptPressure - dl.PressAlertValue'))
                    ->orWhere('ld.PresMin', '>=', DB::raw('dl.OptPressure + dl.PressAlertValue'))
                    ->orWhere('ld.PresMin', '<=', DB::raw('dl.OptPressure - dl.PressAlertValue'))
                    ->orWhere('ld.PresMax', '>=', DB::raw('dl.OptPressure + dl.PressAlertValue'))
                    ->orWhere('ld.PresMax', '<=', DB::raw('dl.OptPressure - dl.PressAlertValue'));
            });

        return $query->get()->keyBy('Line')->toArray();
    }

    public function getAfterHoursPourAlertsCountByDevice($locationId, $deviceId, $startTime, $endTime): array
    {
        $inHours = Location::select(['FromDateTime', 'EndDateTime', 'TotalHours'])->where('LocationID', '=', '' . $locationId)->first();
        $opensAt = $inHours->FromDateTime;
        $closesAt = $inHours->EndDateTime;

        $query = $this->getBaseQuery($locationId, $deviceId, $startTime, $endTime)
            ->leftJoin('LineDataCache as ld', 'dl.Line', '=', 'ld.DeviceLinesID')
            ->where(function ($query) use ($opensAt, $closesAt) {
                $query->where(DB::raw("TIME('ld.ReportDateTime')"), '>', $closesAt)
                    ->where(DB::raw("TIME('ld.ReportDateTime')"), '<', $opensAt);
            })
            ->where('ld.Pulse', '>', 0);

        return $query->get()->keyBy('Line')->toArray();
    }
}
