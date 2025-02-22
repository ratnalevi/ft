<?php

namespace App\Http\Controllers;

use App\Services\LineDataService;
use App\Services\LocationService;
use App\Services\PosDataService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class InRangeController
{
    private PosDataService $posDataService;
    private LineDataService $lineDataService;

    public function __construct(PosDataService $posDataService, LineDataService $lineDataService)
    {
        $this->posDataService = $posDataService;
        $this->lineDataService = $lineDataService;
    }

    public function InRangeReport()
    {
        $locationsUsers = LocationService::LocationDisplay();

        return view('UserReporting.inRangeReport', compact('locationsUsers'));
    }

    /**
     * @OA\Get(
     *     path="/in-range-report-data/{device_id}/{from_date}/{to_date}",
     *     summary="Get In Range Report Data",
     *     tags={"Reporting"},
     *     @OA\Parameter(
     *         name="device_id",
     *         in="path",
     *         description="Device ID (Try 435020109)",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int32",
     *         )
     *     ),
     *     @OA\Parameter(
     *          name="from_date",
     *          in="path",
     *          description="From Date (YYYY-MM-DDTH:i)",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *              format="date-time",
     *          )
     *      ),
     *     @OA\Parameter(
     *           name="to_date",
     *           in="path",
     *           description="To Date (YYYY-MM-DDTH:i)",
     *           required=true,
     *           @OA\Schema(
     *               type="string",
     *               format="date-time",
     *           )
     *       ),
     *     @OA\Response(
     *         response=200,
     *         description="In Range Report Data",
     *         @OA\Schema(ref="#/components/schemas/InRangeReportData"),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function inRangeReportData($deviceId, $fromDate, $endDate): JsonResponse
    {
        // Get floteq data and retrieve ounces
        $fromDate = str_replace("T", " ", $fromDate);
        $endDate = str_replace("T", " ", $endDate);

        $allData = $this->lineDataService->getInRangeDataFromLineData($deviceId, $fromDate, $endDate);
        $tempRangeData = $this->lineDataService->getInRangeDataFromLineData($deviceId, $fromDate, $endDate, 'Temperature');
        $pressRangeData = $this->lineDataService->getInRangeDataFromLineData($deviceId, $fromDate, $endDate, 'Pressure');

        $barTotal = [
            'brand' => 'Bar Total',
            'total_ounces' => 0,
            'in_range_temp_ounces' => 0,
            'in_range_pressure_ounces' => 0,
        ];

        $response = [];
        foreach ($allData as $brand => $datum) {
            $ounces = $datum['ounces'];
            $inRangeTempOunces = $tempRangeData[$brand]['ounces'] ?? 0;
            $inRangePressOunces = $pressRangeData[$brand]['ounces'] ?? 0;
            $brandData = [
                'brand' => $datum['brand'],
                'total_ounces' => number_format($ounces),
                'in_range_temp_ounces' => number_format($inRangeTempOunces),
                'in_range_pressure_ounces' => number_format($inRangePressOunces),
                'in_range_temp_percent' => ROUND(($inRangeTempOunces / $datum['ounces']) * 100, 2),
                'in_range_pressure_percent' => ROUND(($inRangePressOunces / $datum['ounces']) * 100, 2)
            ];
            $response[] = $brandData;

            $barTotal['total_ounces'] += $ounces;
            $barTotal['in_range_temp_ounces'] += $inRangeTempOunces;
            $barTotal['in_range_pressure_ounces'] += $inRangePressOunces;
        }

        if ($barTotal['total_ounces'] > 0) {
            $barTotal['in_range_temp_percent'] = ROUND(($barTotal['in_range_temp_ounces'] / $barTotal['total_ounces']) * 100, 2);
            $barTotal['in_range_pressure_percent'] = ROUND(($barTotal['in_range_pressure_ounces'] / $barTotal['total_ounces']) * 100, 2);
        } else {
            $barTotal['in_range_temp_percent'] = 0;
            $barTotal['in_range_pressure_percent'] = 0;
        }

        $barTotal['total_ounces'] = number_format($barTotal['total_ounces']);
        $barTotal['in_range_temp_ounces'] = number_format($barTotal['in_range_temp_ounces']);
        $barTotal['in_range_pressure_ounces'] = number_format($barTotal['in_range_pressure_ounces']);

        return response()->json(['result' => $response, 'total' => $barTotal]);
    }

    private function comparator($a, $b)
    {
        return $a["ounces"] > $b["ounces"];
    }

}
