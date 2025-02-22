<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PosDataService
{
    public function getPosDataSummaryByDeviceId($location, $startDate, $endDate): array
    {
        $posData = DB::table('POSData as p')
            ->select([
                'bb.Brand as ItemName'
            ])
            ->selectRaw('SUM(Quantity) as QTY, SUM(TotalOunces) as OZ, SUM(TranAMT)/SUM(TotalOunces) as CPO, SUM(TranAMT) AS NetAmt')
            ->join('POSItems as pi', 'p.POSItemsID', '=', 'pi.POSItemsID')
            ->join('BeerBrands as bb', 'bb.BeerBrandsID', '=', 'pi.BeerBrandID')
            ->where('p.LocationID', '=', $location)
            ->where('pi.ItemNUM', '>', 0)
            ->whereRaw("p.DayDateTime >= '$startDate'")
            ->whereRaw("p.DayDateTime <= '$endDate'")
            ->groupBy('bb.Brand')
            ->orderBy('OZ', 'desc')->get();

        $result = [];
        foreach ($posData as $posItem) {
            $result[$posItem->ItemName] = [
                'itemName' => $posItem->ItemName,
                'quantity' => $posItem->QTY,
                'ounces' => $posItem->OZ,
                'pricePerOunce' => $posItem->CPO,
                'netAmt' => $posItem->NetAmt,
            ];
        }

        return $result;
    }
}
