<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PourScoreService
{
    public function getPourScoreByRevenue($location, $startDate, $endDate): array
    {
        $tableName = LineDataService::getDataTableName($startDate, $endDate);

        $posData = DB::table('POSData')
            ->select('POSData.BeerBrandsID', 'POSData.DeviceID', 'BeerBrands.Brand as ItemName', 'POSData.BeerBrandsID')
            ->selectRaw('SUM(POSData.TotalOunces) as totalOunces, SUM(POSData.TranAMT) as TranAmount')
            ->join('POSItems', 'POSData.POSItemsID', '=', 'POSItems.POSItemsID')
            ->join('BeerBrands', 'BeerBrands.BeerBrandsID', '=', 'POSItems.BeerBrandsID')
            ->where('POSData.LocationID', $location)
            ->where('POSData.BeerBrandsID', '!=', 0)
            ->whereBetween('POSData.DayDateTime', [$startDate, $endDate])
            ->groupBy('POSData.BeerBrandsID', 'BeerBrands.Brand', 'POSData.DeviceID')
            ->orderByDesc('totalOunces')
            ->orderByDesc('TranAmount')
            ->get();

        $lineData = DB::table('BeerBrands AS BB')
            ->join('DeviceLines AS DL', 'BB.BeerBrandsID', '=', 'DL.BeerBrandsID')
            ->join($tableName . ' AS LDS', function ($join) {
                $join->on('DL.DevicesID', '=', 'LDS.DevicesID')
                    ->whereRaw('DL.Line = LDS.DeviceLinesID');
            })
            ->whereRaw("(LDS.ReportDateTime) >= '$startDate'")
            ->whereRaw("(LDS.ReportDateTime) <= '$endDate'")
            ->groupBy('LDS.DeviceLinesID', 'DL.BeerBrandsID', 'LDS.DevicesID')
            ->orderByRaw('SUM((LDS.Pulse * DL.OZFactor )/ 29.57) DESC')
            ->select([
                'LDS.DeviceLinesID',
                'DL.BeerBrandsID',
                'LDS.DevicesID',
                DB::raw('ROUND(SUM((LDS.Pulse * DL.OZFactor) / 29.57)) AS totalOunces'),
                DB::raw('FORMAT(SUM(((LDS.Pulse * DL.OZFactor) / 29.57) / 16), 1) AS Pints'),
            ])
            ->get();

        return $this->calculatePourScore($posData, $lineData, 'revenue');
    }

    private function calculatePourScore($posData, $lineData, $type): array
    {
        $matchingPosData = [];
        $remainingPosData = [];
        foreach ($posData as $posItem) {
            $matched = false;

            foreach ($lineData as $lineItem) {
                if ($posItem->DeviceID === $lineItem->DevicesID && $posItem->BeerBrandsID === $lineItem->BeerBrandsID) {
                    $calculatedData = 0;
                    if ($lineItem->totalOunces > 0) {
                        if ($type == 'revenue') {
                            $calculatedData = round((($posItem->TranAmount) / ($lineItem->totalOunces * ($posItem->TranAmount / $posItem->totalOunces))) * 100, 2);
                        } elseif ($type == 'ounces') {
                            $calculatedData = round(($posItem->totalOunces / $lineItem->totalOunces) * 100, 2);
                        }
                    }

                    $matchingPosData[$posItem->ItemName] = [
                        'DeviceID' => $lineItem->DevicesID,
                        'BeerBrandsID' => $lineItem->BeerBrandsID,
                        'ItemName' => $posItem->ItemName,
                        'data' => $calculatedData,
                    ];

                    $matched = true;
                    break;
                }
            }

            if (!$matched) {
                $calculatedData = 0;
                $remainingPosData[$posItem->ItemName] = [
                    'DeviceID' => $posItem->DeviceID,
                    'BeerBrandsID' => $posItem->BeerBrandsID,
                    'ItemName' => $posItem->ItemName,
                    'data' => $calculatedData,
                ];
            }
        }

        return array_replace_recursive($matchingPosData, $remainingPosData);
    }

    public function getPourScoreByOunces($location, $startDate, $endDate): array
    {
        // $posData = DB::table('POSData')
        //     ->select('DeviceID', 'BeerBrandsID', 'ItemName', DB::raw('SUM(TotalOunces) as totalOunces'), DB::raw('SUM(TranAMT) as TranAMT'), DB::raw('SUM(TranAMT) / SUM(TotalOunces) as CostPerOunce'))
        //     ->whereBetween(DB::raw('DATE(DayDateTime)'), [$request->startDateDate, $request->endDateDate])
        //     ->where('BeerBrandsID', '!=', 0)
        //     ->where('LocationID', $location)
        //     ->groupBy('DeviceID', 'BeerBrandsID', 'ItemName')
        //     ->get();

        $tableName = LineDataService::getDataTableName($startDate, $endDate);
        $posData = DB::table('POSData')
            ->select('POSData.BeerBrandsID', 'POSData.DeviceID', 'BeerBrands.Brand as ItemName', 'POSData.BeerBrandsID')
            ->selectRaw('SUM(POSData.TotalOunces) as totalOunces, SUM(POSData.TranAMT) as TranAmount')
            ->join('BeerBrands', 'BeerBrands.BeerBrandsID', '=', 'POSData.BeerBrandsID')
            ->where('POSData.LocationID', $location)
            ->where('POSData.BeerBrandsID', '!=', 0)
            ->whereBetween('POSData.DayDateTime', [$startDate, $endDate])
            ->groupBy('POSData.BeerBrandsID', 'BeerBrands.Brand', 'POSData.DeviceID')
            ->orderByDesc('totalOunces')
            ->orderByDesc('TranAmount')
            ->get();

        $lineData = DB::table('BeerBrands AS BB')
            ->join('DeviceLines AS DL', 'BB.BeerBrandsID', '=', 'DL.BeerBrandsID')
            ->join($tableName . ' AS LDS', function ($join) {
                $join->on('DL.DevicesID', '=', 'LDS.DevicesID')
                    ->whereRaw('DL.Line = LDS.DeviceLinesID');
            })
            ->whereRaw("DATE(ReportDateTime) >= '$startDate'")
            ->whereRaw("DATE(ReportDateTime) <= '$endDate'")
            ->groupBy(
                'LDS.DeviceLinesID',
                'BB.Brand',
                'DL.BeerBrandsID',
                'LDS.DevicesID'
            )
            ->orderByRaw('SUM((LDS.Pulse * DL.OZFactor )/ 29.57) DESC')
            ->select([
                'LDS.DeviceLinesID',
                'BB.Brand',
                'DL.BeerBrandsID',
                'LDS.DevicesID',
                DB::raw('ROUND(SUM((LDS.Pulse * DL.OZFactor) / 29.57)) AS totalOunces'),
                DB::raw('FORMAT(SUM(((LDS.Pulse * DL.OZFactor) / 29.57) / 16), 1) AS Pints'),
            ])
            ->get();

        return $this->calculatePourScore($posData, $lineData, 'ounces');
    }
}
