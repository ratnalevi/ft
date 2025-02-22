<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LineDataService
{
    public function getFromLineData($deviceId, $fromDate, $endDate): Collection
    {
        $table = $this->getDataTableName($fromDate, $endDate);

        return DB::table('DeviceLinesBeerHistory AS DLBH')
            ->select('DLBH.Line', 'BB.Brand', 'DL.OZFactor')
            ->selectRaw('FORMAT(SUM((LDS.Pulse * DL.OZFactor) / 29.57), "#,###") AS Ounces')
            ->selectRaw('FORMAT(SUM(((LDS.Pulse * DL.OZFactor) / 29.57) / 16), "#,###.00") AS Pints')
            ->selectRaw('FORMAT(AVG(LDS.Temp), "#.00") AS AvgTemp')
            ->selectRaw('FORMAT(MAX(LDS.Temp), "#.00") AS MaxTemp')
            ->selectRaw('FORMAT(AVG(LDS.Pres), "#.00") AS AvgPres')
            ->selectRaw('FORMAT(MAX(LDS.Pres), "#.00") AS MaxPres')
            ->selectRaw('FORMAT(AVG(LDS.TDS), "#.00") AS AvgTDS')
            ->selectRaw('IF(ISNULL(DL.LastPourDateTime) OR DL.LastPourDateTime = "0000-00-00 00:00:00", IF(DLBH.FlagWasPoured = true, IF(ISNULL(DLBH.LastPourDateTime) OR DLBH.LastPourDateTime = "0000-00-00 00:00:00", "0000-00-00 00:00:00", DATE_FORMAT(DLBH.LastPourDateTime, "%m-%d-%Y %r")), "0000-00-00 00:00:00"), DATE_FORMAT(DL.LastPourDateTime, "%m-%d-%Y %r")) AS LastPourTime')
            ->selectRaw('IF(ISNULL(DL.LastPourDateTime) OR DL.LastPourDateTime = "0000-00-00 00:00:00", IF(DLBH.FlagWasPoured = true, IF(ISNULL(DLBH.LastPourDateTime) OR DLBH.LastPourDateTime = "0000-00-00 00:00:00", 0, DLBH.LastPour), 0), DL.LastPour) AS LastPourOunces')
            ->join('DeviceLines AS DL', function ($join) {
                $join->on('DL.DevicesID', '=', 'DLBH.DevicesID')
                    ->on('DL.Line', '=', 'DLBH.Line');
            })
            ->join('BeerBrands AS BB', 'BB.BeerBrandsID', '=', 'DLBH.BeerBrandsID')
            ->join($table . ' AS LDS', function ($join) use ($fromDate, $endDate) {
                $join->on('LDS.DevicesID', '=', 'DLBH.DevicesID')
                    ->on('LDS.DeviceLinesID', '=', 'DLBH.Line')
                    ->whereRaw('LDS.ReportDateTime >= ?', $fromDate)
                    ->whereRaw('LDS.ReportDateTime <= ?', $endDate)
                    ->whereRaw('LDS.ReportDateTime >= DLBH.InsertDateTime')
                    ->where(function ($query) {
                        $query->whereRaw('LDS.ReportDateTime < DLBH.UpdateDateTime')
                            ->orWhere('DLBH.UpdateDateTime', '=', '0000-00-00 00:00:00');
                    });
            })
            ->where('DLBH.DevicesID', '=', $deviceId)
            ->where('DL.DevicesID', '=', $deviceId)
            ->whereRaw('LDS.ReportDateTime >= "' . $fromDate . '"')
            ->whereRaw('LDS.ReportDateTime <= "' . $endDate . '"')
            ->groupBy('DLBH.Line', 'BB.Brand', 'DL.OZFactor', 'DLBH.FlagWasPoured', 'LastPourTime', 'LastPourOunces', 'DLBH.LastPour', 'DL.LastPour')
            ->orderByDesc(DB::raw('SUM((LDS.Pulse * DL.OZFactor) / 29.57)'))
            ->orderBy('DLBH.Line', 'asc')
            ->get();
    }

    public static function getDataTableName($startDate, $endDate): string
    {
        $fromDateCarbon = Carbon::parse($startDate);
        $toDateCarbon = Carbon::parse($endDate);
        $daysDifference = $fromDateCarbon->diffInDays($toDateCarbon);

        if ($daysDifference <= 15) {
            $tableName = 'LineDataCache';
        } else {
            $tableName = 'LineData';
        }

        return $tableName;
    }

    public function getInRangeDataFromLineData($deviceId, $startDate, $endDate, $inRangeType = ''): array
    {
        $tableName = $this->getDataTableName($startDate, $endDate);

        $dbQuery = DB::table($tableName . ' AS ld')
            ->select([
                'dl.DeviceLinesID',
                'bb.Brand'
            ])
            ->selectRaw('ROUND(SUM((ld.Pulse * dl.OZFactor)/ 29.57), 2) AS Ounces, ROUND(SUM(((ld.Pulse * dl.OZFactor)/ 29.57)/16)) AS Pints')
            ->join('DeviceLines as dl', 'dl.Line', '=', 'ld.DeviceLinesID')
            ->join('BeerBrands as bb', 'bb.BeerBrandsID', '=', 'dl.BeerBrandsID')
            ->where('ld.DevicesID', '=', $deviceId)
            ->whereRaw('dl.DevicesID = ld.DevicesID')
            ->whereRaw("ReportDateTime >= '$startDate'")
            ->whereRaw("ReportDateTime <= '$endDate'")
            ->where('ld.Pulse', '>', 0);

        if ($inRangeType == 'Temperature') {
            $dbQuery = $dbQuery->whereRaw('ld.Temp >= (dl.OptTemp - dl.TempAlertValue)')
                ->whereRaw('ld.Temp <= (dl.OptTemp + dl.TempAlertValue)');
        } elseif ($inRangeType == 'Pressure') {
            $dbQuery = $dbQuery->whereRaw('ld.Pres >= (dl.OptPressure - dl.PressAlertValue)')
                ->whereRaw('ld.Pres <= (dl.OptPressure + dl.PressAlertValue)');
        }

        $posData = $dbQuery->groupBy(['dl.DeviceLinesID', 'dl.DevicesID', 'bb.Brand'])
            ->orderByRaw('SUM((ld.Pulse * dl.OZFactor) / 29.57) DESC')->get();

        $result = [];
        foreach ($posData as $posItem) {
            $result[$posItem->Brand] = [
                'brand' => $posItem->Brand,
                'ounces' => $posItem->Ounces,
                'pints' => $posItem->Pints,
            ];
        }

        return $result;
    }

}
