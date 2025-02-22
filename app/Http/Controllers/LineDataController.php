<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AlertCenter;
use App\Models\BeerBrand;
use App\Models\DeviceLine;
use App\Models\Devices;
use App\Models\LineData;
use App\Models\Location;
use App\Services\LineDataService;
use App\Services\PosDataService;
use App\Services\PourScoreService;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

class LineDataController extends Controller
{
    private PosDataService $posDataService;
    private LineDataService $lineDataService;

    public function __construct(PosDataService $posDataService, LineDataService $lineDataService)
    {
        $this->posDataService = $posDataService;
        $this->lineDataService = $lineDataService;
    }

    public function test()
    {
        return view('UserManagement.user');
    }

    /**
     * @OA\Get(
     *     path="/get/brand/{location_id}",
     *     summary="List all Brands",
     *     tags={"Brands"},
     *     @OA\Parameter(
     *         name="location_id",
     *         in="path",
     *         description="Location ID (Try ID: 251)",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int32",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="An array of Brands",
     *         @OA\Schema(ref="#/components/schemas/Brands"),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function BrandGetAll($location_id)
    {
        $accounts = DB::table('Location')
            ->select('UserID as AccountID')
            ->where('LocationID', '=', $location_id)
            ->get();

        $data = json_decode($accounts, true);
        if (empty($data)) {
            return [];
        }

        return BeerBrand::select('BeerBrands.BeerBrandsID', 'BeerBrands.Brand')
            ->join('DeviceLines', 'BeerBrands.BeerBrandsID', '=', 'DeviceLines.BeerBrandsID')
            ->join('Devices', 'DeviceLines.DevicesID', '=', 'Devices.DevicesID')
            ->where('Devices.LocationID', $location_id)
            ->distinct() // Retrieve distinct beer brands
            ->get();
    }

    public function GetDevicesAgainstBrand($brand)
    {
        // if all is selected with other options
        $searchForValue = ',';
        if (str_contains($brand, $searchForValue)) {

            $searchForValue = 'all';
            if (str_contains($brand, $searchForValue)) {
                $brand = 'all';
            }
        }

        if ($brand == 'all') {
            $devicesIdsAgainstBrand = DeviceLine::where('BeerBrandsID', '!=', null)->pluck('DevicesID')->all();
        } else {
            $searchForComma = ',';
            // it means multiple brands are selected
            if (str_contains($brand, $searchForComma)) {
                $selectedBrands = explode(',', $brand);
            } else {
                $selectedBrands = [$brand];
            }
            $devicesIdsAgainstBrand = DeviceLine::whereIn('BeerBrandsID', $selectedBrands)->pluck('DevicesID')->all();
        }
        $devices = Devices::whereIn('DevicesID', $devicesIdsAgainstBrand)->get();

        return response()->json(['devices' => $devices]);
    }

    public function reportApi($brand, $type, $daysFilter, $devices)
    {
        $response = [];
        if (!isset($brand)) {
            return $response;
        }

        $sourceData = [['Temperature', 'MaxTemp', 'MinTemp', 'ID', 'DateTimeReport', 'BrandName', 'tag']];

        // if all is selected with other options
        $searchForValue = ',';
        if (str_contains($brand, $searchForValue)) {

            $searchForValue = 'all';
            if (str_contains($brand, $searchForValue)) {
                $brand = 'all';
            }
        }

        if ($brand == 'all') {
            $devicesIdsAgainstBrand = DeviceLine::where('BeerBrandsID', '!=', null)->pluck('DevicesID')->all();

            if ($daysFilter == 'all') {
                //                $lineData = LineData::whereIn('DevicesID', $devicesIdsAgainstBrand)->get();
                $lineData = LineData::whereIn('DevicesID', $devicesIdsAgainstBrand)->orderBy('LineDataID', 'desc')->take(20)->get();
            } else {
                $lineData = LineData::whereIn('DevicesID', $devicesIdsAgainstBrand)->where('ReportDateTime', '>', now()->subDays($daysFilter)->endOfDay())->orderBy('LineDataID', 'desc')->take(20)->get();
            }


            foreach ($lineData as $value) {

                $BrandId = DeviceLine::where('DevicesID', $value->DevicesID)->pluck('BeerBrandsID')->first();
                $Brand = BeerBrand::where('BeerBrandsID', $BrandId)->pluck('Brand')->first();

                if ($type == 'temp') {
                    $sourceData[] = [$value->Temp, $value->TempMax, $value->TempMin, $value->DevicesID, $value->ReportDateTime, $Brand, '_&%' . $value->DevicesID];
                }
                if ($type == 'pre') {
                    $sourceData[] = [$value->Pres, $value->TempMax, $value->TempMin, $value->DevicesID, $value->ReportDateTime, $Brand, '_&%' . $value->DevicesID];
                }
                if ($type == 'tds') {
                    $sourceData[] = [$value->TDS, $value->TempMax, $value->TempMin, $value->DevicesID, $value->ReportDateTime, $Brand, '_&%' . $value->DevicesID];
                }
            }
            return response()->json($sourceData);
        } else {

            $searchForValue = ',';

            // it means multiple brands are selected
            if (str_contains($brand, $searchForValue)) {
                $selectedBrands = explode(',', $brand);
            } else {
                $selectedBrands = [$brand];
            }

            $devicesIds = DeviceLine::whereIn('BeerBrandsID', $selectedBrands)->pluck('DevicesID')->all();

            if ($daysFilter == 'all') {
                //                $lineData = LineData::whereIn('DevicesID', $devicesIds)->get();
                $lineData = LineData::whereIn('DevicesID', $devicesIds)->orderBy('LineDataID', 'desc')->take(5)->get();
            } else {
                //                $lineData = LineData::whereIn('DevicesID', $devicesIds)->where('ReportDateTime', '>', now()->subDays($daysfilter)->endOfDay())->get();
                $lineData = LineData::whereIn('DevicesID', $devicesIds)->where('ReportDateTime', '>', now()->subDays($daysFilter)->endOfDay())->orderBy('LineDataID', 'desc')->take(5)->get();
            }

            foreach ($lineData as $value) {
                if ($devices == 'all') {
                    $Brand = BeerBrand::where('BeerBrandsID', $brand)->pluck('Brand')->first();

                    if ($type == 'temp') {
                        $sourceData[] = [$value->Temp, $value->TempMax, $value->TempMin, $value->DevicesID, $value->ReportDateTime, $Brand, '_&%' . $value->DevicesID];
                    }
                    if ($type == 'pre') {
                        $sourceData[] = [$value->Pres, $value->TempMax, $value->TempMin, $value->DevicesID, $value->ReportDateTime, $Brand, '_&%' . $value->DevicesID];
                    }
                    if ($type == 'tds') {
                        $sourceData[] = [$value->TDS, $value->TempMax, $value->TempMin, $value->DevicesID, $value->ReportDateTime, $Brand, '_&%' . $value->DevicesID];
                    }
                } else {

                    $selectedDevices = explode(',', $devices);

                    if (in_array($value->DevicesID, $selectedDevices)) {
                        $Brand = BeerBrand::where('BeerBrandsID', $brand)->pluck('Brand')->first();

                        if ($type == 'temp') {
                            $sourceData[] = [$value->Temp, $value->TempMax, $value->TempMin, $value->DevicesID, $value->ReportDateTime, $Brand, '_&%' . $value->DevicesID];
                        }
                        if ($type == 'pre') {
                            $sourceData[] = [$value->Pres, $value->TempMax, $value->TempMin, $value->DevicesID, $value->ReportDateTime, $Brand, '_&%' . $value->DevicesID];
                        }
                        if ($type == 'tds') {
                            $sourceData[] = [$value->TDS, $value->TempMax, $value->TempMin, $value->DevicesID, $value->ReportDateTime, $Brand, '_&%' . $value->DevicesID];
                        }
                    }
                }
            }

            return response()->json($sourceData);
        }
    }

    public function SensorReporting($beerbrandID, $temperature, $daysfilter, $deviceid)
    {
        $lineData = DB::table('LineData')
            ->leftjoin('Devices', 'LineData.DevicesID', '=', 'Devices.DevicesID')
            ->leftJoin('DeviceLines', 'Devices.DevicesID', '=', 'DeviceLines.DevicesID')
            ->leftjoin('BeerBrands', 'Devicelines.BeerBrandsID', '=', 'BeerBrands.BeerBrandsID')
            // ->where('linedata.DevicesID', '=', $deviceid)
            ->where('ReportDateTime', '>', now()->subDays(30)->endOfDay())
            ->where('BeerBrands.RecordStatus', '1')
            ->orderBy('ReportDateTime', 'asc')
            ->get();


        return response()->json([
            'Temperature' => $temperature,
            'beerBrand' => $beerbrandID,
            'DevicesID' => $deviceid,
            'LineData' => $lineData,
        ]);
    }

    public function getDevicesLine($id)
    {
        return DeviceLine::where('DevicesID', $id)->get();
    }

    public function getDevicesIds($brand)
    {
        $searchForValue = ',';

        // it means multiple brands are selected
        if (str_contains($brand, $searchForValue)) {
            $selectedBrands = explode($searchForValue, $brand);
        } else {
            $selectedBrands = [$brand];
        }

        $deviceLine = DeviceLine::whereIn('BeerBrandsID', $selectedBrands)->pluck('DevicesID')->all();
        $devices = Devices::whereIn('DevicesID', $deviceLine)->get();

        return response()->json(['devices' => $devices]);
    }

    /**
     * @OA\Get(
     *     path="/pour-score-detail-data/{device_id}/{from_date}/{to_date}",
     *     summary="Get Pour Score Detail Report Data",
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
     *         description="Pour Score Detail Report Data",
     *         @OA\Schema(ref="#/components/schemas/PourScoreDetailReport"),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function pourScoreDetail($deviceId, $fromDate, $endDate): JsonResponse
    {
        $device = Devices::where('DevicesID', $deviceId)->first();
        $data = $this->getPourScoreData($device->LocationID, $deviceId, $fromDate, $endDate, $type = 'Detail');
        $barTotal = $data['barTotal'];
        $response = $data['data'];

        $barExpAmt = $barTotal['Exp Amt'];

        $barTotal['PourScore'] = $barTotal['FT OZ'] > 0 ? round(($barTotal['POS OZ'] / $barTotal['FT OZ']) * 100) : 0;
        $barTotal['PourScore - $'] = $barExpAmt > 0 ? round(($barTotal['POS NET'] / $barExpAmt) * 100) : 0;

        $barTotal['FT Pints'] = number_format($barTotal['FT OZ'] / 16);
        $barTotal['POS NET'] = number_format($barTotal['POS NET'], 2);
        $barTotal['Exp Amt'] = number_format($barExpAmt, 2);

        $barTotal['POS OZ'] = number_format($barTotal['POS OZ']);
        $barTotal['FT OZ'] = number_format($barTotal['FT OZ']);
        $barTotal['POS Pints'] = number_format($barTotal['POS Pints']);

        return response()->json(['result' => $response, 'total' => $barTotal]);
    }

    public function getPourScoreData($location, $device, $fromDate, $endDate, $type = 'Report'): array
    {
        $fromDate = str_replace("T", " ", $fromDate);
        $endDate = str_replace("T", " ", $endDate);

        $lineDataItems = $this->lineDataService->getFromLineData($device, $fromDate, $endDate);

        $floteqData = [];
        foreach ($lineDataItems as $item) {
            $floteqData[$item->Brand] = [
                'ounces' => intval(str_replace(',', '', $item->Ounces)),
            ];
        }

        // Get pos data
        $posData = $this->posDataService->getPosDataSummaryByDeviceId($location, $fromDate, $endDate);
        $price = 0.45;
        $barTotal = [
            'Brand' => 'Bar Total',
            'POS Pints' => 0,
            'POS OZ' => 0,
            'FT Pints' => 0,
            'FT OZ' => 0,
            'POS NET' => 0.00,
            'Exp Amt' => 0.00,
            'Price' => $price,
        ];
        $response = [];

        foreach ($posData as $brand => $datum) {
            $brandPrice = round($datum['pricePerOunce'], 2);
            $posOunces = intval($datum['ounces']);
            $posPints = intval($datum['quantity']);
            $posNetAmt = floatval($datum['netAmt']);

            $floteqOunces = intval($floteqData[$brand]['ounces'] ?? 0);
            $floteqPints = $floteqOunces / 16;
            $floteqAmt = round(($floteqData[$brand]['ounces'] ?? 0) * $brandPrice, 2);
            $pourScoreOunces = $floteqOunces > 0 ? round(($posOunces / $floteqOunces) * 100) : 0;
            $pourScoreRevenue = $floteqAmt > 0 ? round(($posNetAmt / $floteqAmt) * 100) : 0;

            if ($type == 'Detail') {
                $brandData = [
                    'Brand' => $brand,
                    'POS Pints' => number_format($posPints),
                    'POS OZ' => number_format($posOunces),
                    'FT Pints' => number_format($floteqPints),
                    'FT OZ' => number_format($floteqOunces),
                    'PourScore' => $pourScoreOunces,
                    'POS NET' => number_format($posNetAmt, 2),
                    'Exp Amt' => number_format($floteqAmt, 2),
                    'PourScore - $' => $pourScoreRevenue,
                    'Price' => $brandPrice,
                ];

                $barTotal['POS Pints'] += $posPints;
                $barTotal['POS OZ'] += $posOunces;
                $barTotal['FT OZ'] += $floteqOunces;
                $barTotal['POS NET'] += $posNetAmt;
                $barTotal['Exp Amt'] += $floteqAmt;

                $response[] = $brandData;
            } elseif ($type == 'Report') {
                $response['revenue'][$brand] = [
                    'data' => $pourScoreRevenue,
                    'ItemName' => $brand,
                ];

                $response['ounces'][$brand] = [
                    'data' => $pourScoreOunces,
                    'ItemName' => $brand,
                ];
            }
        }

        return ['data' => $response, 'barTotal' => $barTotal];
    }

    public function pourscoreReport(Request $request, $location, $types): JsonResponse
    {
        $fromDate = $request->get('startDate');
        $endDate = $request->get('endDate');

        $device = Devices::where('LocationID', $location)->first();
        $data = $this->getPourScoreData($location, $device->DevicesID, $fromDate, $endDate, $type = 'Report');
        $response = $data['data'];

        return response()->json($types == 'revenue' ? $response['revenue'] : $response['ounces']);
    }

    public function brandComparison($location = null, $brand = null, $types = null, $from = null, $to = null)
    {
        $from = str_replace("T", " ", $from);
        $to = str_replace("T", " ", $to);

        $devicesBrand = array_filter(explode(',', $brand));
        $tableName = LineDataService::getDataTableName($from, $to);

        if ($types == 'ounces') {
            $result = DB::table('BeerBrands')
                ->select('Brand', DB::raw('Floor(SUM((Pulse * 1.15) / 29.57)) AS Ounces'))
                ->join('DeviceLines as DL', 'BeerBrands.BeerBrandsID', '=', 'DL.BeerBrandsID')
                ->join('Devices as D', 'DL.DevicesID', '=', 'D.DevicesID')
                ->where('D.LocationID', $location)
                ->where('D.RecordStatus', '!=', 2)
                ->join($tableName . ' as LD', function ($join) use ($from, $to) {
                    $join->on('DL.DevicesID', '=', 'LD.DevicesID')
                        ->whereRaw('DL.Line = LD.DeviceLinesID')
                        ->whereBetween('ReportDateTime', [$from, $to]);
                })
                ->groupBy('Brand')
                ->orderByDesc(DB::raw('SUM(Pulse)'));
        } else {
            return [];
        }

        if ($brand != 'All') {
            $result = $result->whereIn('DL.BeerBrandsID', $devicesBrand);
        }

        return $result->get();
    }

    public function TrendAnalysis($locationid): JsonResponse
    {

        $devicesIds = Devices::where('LocationID', $locationid)->pluck('DevicesID')->all();

        //        $totalPulses = LineData::whereIn('DevicesID', $devicesIds)->sum('Pulse');
        $totalPulses = LineData::whereIn('DevicesID', $devicesIds)->orderBy('LineDataID', 'desc')->take(20)->sum('Pulse');


        $ounces = round(($totalPulses * 1.12) / 29.57, 2);


        $accountIds = Account::where('LocationID', $locationid)->pluck('AccountID')->all();
        $sumOfOuncesSold = DB::table('Sample_POS_Data')->whereIn('accountid', $accountIds)->sum('ounces_sold');


        $ouncesSold = round($sumOfOuncesSold - $ounces, 2);


        return response()->json(['ounces' => $ounces, 'ouncesSold' => $ouncesSold]);
    }

    public function saveDevice(Request $request)
    {
        $name = Session::get('userID');
        $currentDateTime = Carbon::now();

        $request->validate([

            'optTemp' => "required|numeric|min:0",
            'opt_pressure' => "required|numeric|min:0",
            // 'temp_press_alert'     =>  "required",
            // 'temp_press_alert_timeout' =>  "required",
            'temp_alert_value' => "required|numeric|min:0",
            'press_alert_value' => "required|numeric|min:0",
            // 'pressure'           =>  "required|numeric|min:0",
            'keg_cost' => "required|numeric|min:0",
            'line_length' => "required|numeric|min:0",
            'line' => "required|numeric|min:0",

        ]);

        $existingDeviceLine = DB::table('DeviceLines')
            ->where('Line', $request->line)
            ->where('DevicesID', $request->device_name)
            ->first();

        if ($existingDeviceLine) {
            // If the record exists, update it
            $newDeviceLineID = DB::table('DeviceLines')
                ->where('Line', $request->line)
                ->where('DevicesID', $request->device_name)
                ->update([
                    'DevicesID' => $request->device_name,
                    'BeerBrandsID' => $request->brand,
                    'BeerTubingID' => 2,
                    'KegTypeID' => $request->keg_type,
                    'DistAccountID' => $request->distributor,
                    'OptTemp' => $request->optTemp,
                    'OZFactor' => $request->OZFactor,
                    'OptPressure' => $request->opt_pressure,
                    'TempAlertValue' => $request->temp_alert_value,
                    'PressAlertValue' => $request->press_alert_value,
                    'KegCost' => $request->keg_cost,
                    'LineLength' => $request->line_length,
                ]);
            $existingBeerHistory = DB::table('DeviceLinesBeerHistory')->where('DevicesID', $request->device_name)
                ->where('Line', $request->line)
                ->where('BeerBrandsID', $request->brand)
                ->where('KegTypeID', $request->keg_type)
                ->where('DistAccountID', $request->distributor)
                ->first();
            if ($existingBeerHistory) {
                $LastPour = $existingDeviceLine->LastPour;
                $LastPourDateTime = $existingDeviceLine->LastPourDateTime;
                DB::table('DeviceLinesBeerHistory')->where('DevicesID', $request->device_name)
                    ->where('Line', $request->line)
                    ->where('BeerBrandsID', $request->brand)
                    ->update([
                        'UpdateDateTime' => Carbon::now(),
                        'LastPour' => $LastPour,
                        'LastPourDateTime' => $LastPourDateTime,
                        'FlagWasPoured' => true,
                    ]);
            }
            $DeviceLinesID = $existingDeviceLine->DeviceLinesID;
        } else {
            // If the record doesn't exist, insert a new row
            $newDeviceLineID = DB::table('DeviceLines')->insert([
                'DevicesID' => $request->device_name,
                'BeerBrandsID' => $request->brand,
                'BeerTubingID' => 2,
                'KegTypeID' => $request->keg_type,
                'DistAccountID' => $request->distributor,
                'OZFactor' => $request->OZFactor,
                'OptTemp' => $request->optTemp,
                'OptPressure' => $request->opt_pressure,
                'TempAlertValue' => $request->temp_alert_value,
                'PressAlertValue' => $request->press_alert_value,
                'KegCost' => $request->keg_cost,
                'Line' => $request->line,
                'LineLength' => $request->line_length,
            ]);

            // Fetch the last inserted record
            $data = DB::table('DeviceLines')->orderBy('DeviceLinesID', 'desc')->first();
            $DeviceLinesID = $data->DeviceLinesID;
        }

        // insertion and updation in DeviceLinesBeerHistory (updation depends on condtion if he already exists)
        DB::table('DeviceLinesBeerHistory')->insert([
            'DevicesID' => $request->device_name,
            'Line' => $request->line,
            'BeerBrandsID' => $request->brand,
            'OZFactor' => $request->OZFactor,
            'KegTypeID' => $request->keg_type,
            'DeviceLinesID' => $DeviceLinesID,
            'InsertDateTime' => Carbon::now(),
            'DistAccountID' => $request->distributor,
            'LastPour' => 0,
            'LastPourDateTime' => '0000-00-00 00:00:00',
            'FlagWasPoured' => false,
            'UpdateDateTime' => '0000-00-00 00:00:00',
        ]);

        Log::channel('custom_log')->info('Insertion:- Line: \'' . $request->line . '\' for Device \'' . $request->device_name . '\' added by \'' . $name->UserID . '\' at ' . Carbon::now());
        if ($newDeviceLineID) {
            return redirect('/line-management')->with('success', 'Line added successfully.');
            // return redirect()->back()->with('message', 'Line added successfully !');
        } else {
            return redirect()->back()->with('message', 'Something went wrong while adding this device !');
        }
    }

    /*    public function updateDevice(Request $request)
    {
        $name = Session::get('userID');
        $request->validate([
            'optTemp'             =>  "required|numeric|min:0",
            'opt_pressure'        =>  "required|numeric|min:0",
            'temp_alert_value'          =>  "required|numeric|min:0",
            'press_alert_value'    =>  "required|numeric|min:0",
            'keg_cost'           =>  "required|numeric|min:0",
            'line_length'        =>  "required|numeric|min:0",
            'line'        =>  "required|numeric|min:0"
        ]);
        $deviceLineID = $request->hidden_deviceline;
        $selected_brand = $request->selected_brand;
        $old_oz = $request->old_oz;
        $selected_keg = $request->selected_keg;
        $selected_distributer = $request->selected_distributer;
        $deviceLine = DB::table('DeviceLines')
            ->where('DevicesID', $request->selected_device)
            ->where('Line', $request->line)
            ->get()->first();
        if ($deviceLine) {
            try {
                $deviceLines =   DB::table('DeviceLines')
                    ->select('DeviceLinesID', 'InsertDateTime', 'BeerBrandsID', 'LastPour', 'LastPourDateTime')
                    ->where('DevicesID', $request->selected_device)
                    ->where('Line', $request->line)
                    ->first();
                $deviceLinesID = $deviceLines->DeviceLinesID;
                $insertDateTime = $deviceLines->InsertDateTime;
                $beerBrandsID = $deviceLines->BeerBrandsID;
                $LastPour = $deviceLines->LastPour;
                $LastPourDateTime = $deviceLines->LastPourDateTime;

                //only updating if the brand is also changed
                if ($selected_brand != $request->brand || $selected_keg != $request->keg_type || $selected_distributer != $request->distributor) {
                    DB::table('DeviceLinesBeerHistory')->where('DevicesID', $request->selected_device)
                        ->where('Line', $request->line)
                        ->where('BeerBrandsID', $selected_brand)
                        ->where('KegTypeID', $selected_keg)
                        ->where('DistAccountID', $selected_distributer)
                        ->update([
                            'UpdateDateTime' => Carbon::now(),
                            'LastPour' => $LastPour,
                            'LastPourDateTime' => $LastPourDateTime,
                            'FlagWasPoured' => True,
                        ]);
                } elseif ($old_oz != $request->OZFactor) {
                    DB::table('DeviceLinesBeerHistory')->where('DevicesID', $request->selected_device)
                        ->where('Line', $request->line)
                        ->where('BeerBrandsID', $selected_brand)
                        ->where('KegTypeID', $selected_keg)
                        ->where('DistAccountID', $selected_distributer)
                        ->update([
                            'OZFactor' => $request->OZFactor,
                        ]);


                    DB::table('DeviceLinesHistory')->insert([
                        'DeviceLinesID'       => $deviceLinesID,
                        'DevicesID'       => $request->selected_device,
                        'BeerBrandsID'    => $request->brand,
                        'BeerTubingID'    => 2,
                        'KegTypeID'       => $request->keg_type,
                        'DistAccountID'   => $request->distributor,
                        'OZFactor'         => $request->OZFactor,
                        'OptTemp'         => $request->optTemp,
                        'OptPressure'     => $request->opt_pressure,
                        'TempAlertValue'  => $request->temp_alert_value,
                        'PressAlertValue' => $request->press_alert_value,
                        'KegCost'         => $request->keg_cost,
                        'Line' => $request->line,
                        'LineLength'      => $request->line_length,
                    ]);
                }

                DB::table('DeviceLines')
                    ->where('DeviceLinesID', $deviceLineID)
                    ->update(array(
                        'DevicesID'       => $request->selected_device,
                        'BeerBrandsID'    => $request->brand,
                        'BeerTubingID'    => 2,
                        'KegTypeID'       => $request->keg_type,
                        'DistAccountID'   => $request->distributor,
                        'OZFactor'         => $request->OZFactor,
                        'OptTemp'         => $request->optTemp,
                        'OptPressure'     => $request->opt_pressure,
                        'TempAlertValue'  => $request->temp_alert_value,
                        'PressAlertValue' => $request->press_alert_value,
                        'KegCost'         => $request->keg_cost,
                        'Line' => $request->line,
                        'LineLength'      => $request->line_length,
                        'UpdateDateTime' => Carbon::now()
                    ));
                //only updating if the brand is also changed
                if ($selected_brand != $request->brand || $selected_keg != $request->keg_type || $selected_distributer != $request->distributor) {
                    DB::table('DeviceLinesBeerHistory')->insert([
                        'DevicesID' => $request->selected_device,
                        'Line' => $request->line,
                        'BeerBrandsID' => $request->brand,
                        'OZFactor'         => $request->OZFactor,
                        'KegTypeID' => $request->keg_type,
                        'DeviceLinesID' => $deviceLinesID,
                        'InsertDateTime' => Carbon::now(),
                        'DistAccountID' => $request->distributor,
                        'LastPour' => 0,
                        'LastPourDateTime' => '0000-00-00 00:00:00',
                        'FlagWasPoured' => False,
                        'UpdateDateTime' => '0000-00-00 00:00:00',
                    ]);
                }
                Log::channel('custom_log')->info('Updation:- Line: \'' . $request->line . '\' for Device \'' . $request->selected_device . '\' where old oz factor is:  \'' . $old_oz . '\' and new oz factor is: \'' . $request->OZFactor . '\' updated by \'' . $name->UserID . '\' at ' . Carbon::now());
                return redirect('line-management')->with([
                    'success' => 'Line Updated successfully!',
                    'selected_location' => $request->selected_location,
                    'selected_device' => $request->selected_device,
                ]);
                // return redirect('line-management')->with('success', 'Line Updated successfully !');
                // return redirect()->back()->with('message', 'Updated successfully !');
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }
    }
*/

    public function updateDevice(Request $request)
    {
        $name = Session::get('userID');
        $request->validate([
            'optTemp' => "required|numeric|min:0",
            'opt_pressure' => "required|numeric|min:0",
            'temp_alert_value' => "required|numeric|min:0",
            'press_alert_value' => "required|numeric|min:0",
            'keg_cost' => "required|numeric|min:0",
            'line_length' => "required|numeric|min:0",
            'line' => "required|numeric|min:0"
        ]);
        $deviceLineID = $request->hidden_deviceline;
        $selected_brand = $request->selected_brand;
        $old_oz = $request->old_oz;
        $selected_keg = $request->selected_keg;
        $selected_distributer = $request->selected_distributer;
        $deviceLine = DB::table('DeviceLines')
            ->where('DevicesID', $request->selected_device)
            ->where('Line', $request->line)
            ->get()->first();
        if ($deviceLine) {
            try {
                $deviceLines = DB::table('DeviceLines')
                    ->select('DeviceLinesID', 'InsertDateTime', 'BeerBrandsID', 'LastPour', 'LastPourDateTime')
                    ->where('DevicesID', $request->selected_device)
                    ->where('Line', $request->line)
                    ->first();
                $deviceLinesID = $deviceLines->DeviceLinesID;
                $insertDateTime = $deviceLines->InsertDateTime;
                $beerBrandsID = $deviceLines->BeerBrandsID;
                $LastPour = $deviceLines->LastPour;
                $LastPourDateTime = $deviceLines->LastPourDateTime;

                //only updating if the brand is also changed
                if ($selected_brand != $request->brand || $selected_keg != $request->keg_type || $selected_distributer != $request->distributor) {
                    DB::table('DeviceLinesBeerHistory')->where('DevicesID', $request->selected_device)
                        ->where('Line', $request->line)
                        ->where('BeerBrandsID', $selected_brand)
                        ->where('KegTypeID', $selected_keg)
                        ->where('DistAccountID', $selected_distributer)
                        ->update([
                            'UpdateDateTime' => Carbon::now(),
                            'LastPour' => $LastPour,
                            'LastPourDateTime' => $LastPourDateTime,
                            'FlagWasPoured' => true,
                        ]);
                } elseif ($old_oz != $request->OZFactor) {
                    DB::table('DeviceLinesBeerHistory')->where('DevicesID', $request->selected_device)
                        ->where('Line', $request->line)
                        ->where('BeerBrandsID', $selected_brand)
                        ->where('KegTypeID', $selected_keg)
                        ->where('DistAccountID', $selected_distributer)
                        ->update([
                            'OZFactor' => $request->OZFactor,
                        ]);

                    DB::table('DeviceLines')->where('DevicesID', $request->selected_device)
                        ->where('Line', $request->line)
                        ->where('BeerBrandsID', $selected_brand)
                        ->where('KegTypeID', $selected_keg)
                        ->where('DistAccountID', $selected_distributer)
                        ->update([
                            'OZFactor' => $request->OZFactor,
                        ]);

                    DB::table('DeviceLinesHistory')->insert([
                        'DeviceLinesID' => $deviceLinesID,
                        'DevicesID' => $request->selected_device,
                        'BeerBrandsID' => $request->brand,
                        'BeerTubingID' => 2,
                        'KegTypeID' => $request->keg_type,
                        'DistAccountID' => $request->distributor,
                        'OZFactor' => $request->OZFactor,
                        'OptTemp' => $request->optTemp,
                        'OptPressure' => $request->opt_pressure,
                        'TempAlertValue' => $request->temp_alert_value,
                        'PressAlertValue' => $request->press_alert_value,
                        'KegCost' => $request->keg_cost,
                        'Line' => $request->line,
                        'LineLength' => $request->line_length,
                    ]);
                }

                DB::table('DeviceLines')
                    ->where('DeviceLinesID', $deviceLineID)
                    ->update(array(
                        'DevicesID' => $request->selected_device,
                        'BeerBrandsID' => $request->brand,
                        'BeerTubingID' => 2,
                        'KegTypeID' => $request->keg_type,
                        'DistAccountID' => $request->distributor,
                        'OZFactor' => $request->OZFactor,
                        'OptTemp' => $request->optTemp,
                        'OptPressure' => $request->opt_pressure,
                        'TempAlertValue' => $request->temp_alert_value,
                        'PressAlertValue' => $request->press_alert_value,
                        'KegCost' => $request->keg_cost,
                        'Line' => $request->line,
                        'LineLength' => $request->line_length,
                        'UpdateDateTime' => Carbon::now()
                    ));
                //only updating if the brand is also changed
                if ($selected_brand != $request->brand || $selected_keg != $request->keg_type || $selected_distributer != $request->distributor) {
                    DB::table('DeviceLinesBeerHistory')->insert([
                        'DevicesID' => $request->selected_device,
                        'Line' => $request->line,
                        'BeerBrandsID' => $request->brand,
                        'OZFactor' => $request->OZFactor,
                        'KegTypeID' => $request->keg_type,
                        'DeviceLinesID' => $deviceLinesID,
                        'InsertDateTime' => Carbon::now(),
                        'DistAccountID' => $request->distributor,
                        'LastPour' => 0,
                        'LastPourDateTime' => '0000-00-00 00:00:00',
                        'FlagWasPoured' => false,
                        'UpdateDateTime' => '0000-00-00 00:00:00',
                    ]);
                }
                Log::channel('custom_log')->info('Updation:- Line: \'' . $request->line . '\' for Device \'' . $request->selected_device . '\' where old oz factor is:  \'' . $old_oz . '\' and new oz factor is: \'' . $request->OZFactor . '\' updated by \'' . $name->UserID . '\' at ' . Carbon::now());
                return redirect('line-management')->with([
                    'success' => 'Line Updated successfully!',
                    'selected_location' => $request->selected_location,
                    'selected_device' => $request->selected_device,
                ]);
                // return redirect('line-management')->with('success', 'Line Updated successfully !');
                // return redirect()->back()->with('message', 'Updated successfully !');
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }

    public function lineSummary(Request $request)
    {
        $data = DB::table('BeerBrands')
            ->join('DeviceLines AS DL', 'BeerBrands.BeerBrandsID', '=', 'DL.BeerBrandsID')
            ->join('LineData AS LD', function ($join) {
                $join->on('DL.DevicesID', '=', 'LD.DevicesID')
                    ->on('DL.DeviceLinesID', '=', 'LD.DeviceLinesID');
            })
            ->select(
                'DL.Line',
                'Brand',
                DB::raw("FORMAT(SUM((Pulse * 100.15) / 29.57), '#,###') AS Ounces"),
                DB::raw("FORMAT(SUM(((Pulse * 1.15) / 29.57) / 16), '#,###.00') AS Pints"),
                DB::raw("FORMAT(AVG(Temp), '#.00') AS AvgTemp"),
                DB::raw("FORMAT(MAX(TempMax), '#.00') AS MaxTemp"),
                DB::raw("FORMAT(AVG(Pres), '#.00') AS AvgPres"),
                DB::raw("FORMAT(MAX(PresMax), '#.00') AS MaxPres"),
                DB::raw("FORMAT(AVG(TDS), '#.00') AS AvgTDS"),
                DB::raw("MAX(ReportDateTime) AS LastPourTime")
            )
            ->where('Pulse', '>', 0)
            ->whereBetween('ReportDateTime', ['2023-04-01 00:00:00', '2023-04-30 00:00:00'])
            ->groupBy('DL.Line', 'Brand')
            ->orderBy('Brand')
            ->get();

        $locations = Location::all();
        return view('UserReporting.line_summary', compact('data', 'locations'));
    }

    public function LoadLineSummary($location, $pagenumber)
    {
        $devices = Devices::where("LocationID", $location)->pluck('DevicesID')->all();

        $data = LineData::whereIn("DevicesID", $devices)->select(DB::raw('sum(Pulse) as total_pulse, DeviceLinesID,
              count(*) as totalrecords,
              sum(Temp) as totalTemp,
              sum(Pres) as totalPres,
              sum(TDS) as totalTds'))
            ->groupBy('DeviceLinesID')
            ->get();


        return response()->json(['data' => $data]);
    }

    public function saveLocation(Request $request)
    {
        $name = Session::get('userID');
        $timeFrom = date("H:i:s", strtotime($request->from));
        $timeUntil = date("H:i:s", strtotime($request->until));

        $totalHours = $request->input('totalHours');
        $startTime = new DateTime($timeFrom);
        $endTime = clone $startTime;
        $endTime->modify("+{$totalHours} hours");
        $formattedEndTime = $endTime->format('H:i:s');

        $form_data = array(
            'UserID' => $request->AccountID,
            'LocationName' => $request->LocationName,
            'PhonePrimary' => $request->phoneNumber,
            'PhoneSeconday' => $request->phoneNumber,
            'Email' => $request->email,
            'LocationType' => 1,
            'LocationDESC' => $request->contactName,
            'Address1' => $request->address1,
            'Address2' => $request->Address2,
            'PostalCode' => $request->zip,
            'City' => $request->city,
            'State' => $request->state,
            'FromDateTime' => $timeFrom,
            'EndDateTime' => $formattedEndTime,
            'TotalHours' => $totalHours,
        );

        $location = Location::create($form_data);
        $lastlocation = Location::orderBy('LocationID', 'DESC')->first();
        $brands = $request->brands;
        $devices = json_encode($brands);


        if (is_array($brands)) {
            foreach ($brands as $deivce) {
                $deviceName = Devices::where('DevicesID', $deivce)->first();
                if ($deviceName) {
                    DB::table('Devices')->where('DevicesID', $deivce)
                        ->update(['LocationID' => $lastlocation->LocationID,]);
                } else {
                    $data = array(
                        'DevicesID' => $deivce,
                        'LocationID' => $lastlocation->LocationID,
                        'Name' => $deviceName->Name,
                    );
                    Devices::create($data);
                }
            }
        } else {
            //not adding any devices
        }
        Log::channel('custom_log')->info('Insertion:- Location: \'' . $request->LocationName . '\' added by \'' . $name->UserID . '\' at ' . Carbon::now());
        return redirect('/location-management')->with('success', 'Location Added Successfully.');

        // return redirect()->back()->with('success', 'Location Added Successfully');
    }

    public function updateLocation(Request $request)
    {
        $name = Session::get('userID');
        $res = Validator::make($request->all(), [
            "brands" => "required",
        ]);
        foreach ($res->errors()->toArray() as $field => $message) {
            $errors[] = [
                'message' => $message[0],
            ];
        }
        if (isset($errors)) {
            return redirect()->back()->with('error', 'You can not Update location without device');
        }
        $timeFrom = date("H:i:s", strtotime($request->from));
        $timeUntil = date("H:i:s", strtotime($request->until));

        $totalHours = $request->input('totalHours');
        $startTime = new DateTime($timeFrom);
        $endTime = clone $startTime;
        $endTime->modify("+{$totalHours} hours");
        $formattedEndTime = $endTime->format('H:i:s');
        Location::where('LocationID', $request->LocationID)->update(
            [
                'UserID' => $request->AccountID,
                'LocationName' => $request->LocationName,
                'PhonePrimary' => $request->phoneNumber,
                'PhoneSeconday' => $request->phoneNumber,
                'Email' => $request->email,
                'LocationDESC' => $request->contactName,
                'Address1' => $request->address1,
                'Address2' => $request->address2,
                'PostalCode' => $request->zip,
                'City' => $request->city,
                'State' => $request->state,
                'FromDateTime' => $timeFrom,
                'EndDateTime' => $formattedEndTime,
                'TotalHours' => $totalHours,
            ]
        );

        // $lastlocation = Location::orderBy('LocationID', 'DESC')->first();
        $LocationID = $request->LocationID;

        $brands = $request->brands;
        $devices = json_encode($brands);

        DB::table('Devices')->where('LocationID', $LocationID)
            ->whereNotIn('DevicesID', $brands)
            ->update(
                [
                    'LocationID' => 0,
                ]
            );
        foreach ($brands as $deivce) {
            $deviceName = Devices::where('DevicesID', $deivce)->first();
            if ($deviceName) {
                DB::table('Devices')->where('DevicesID', $deivce)
                    ->update(
                        [
                            'LocationID' => $LocationID,
                        ]
                    );
            } else {

                $data = array(
                    'DevicesID' => $deivce,
                    'LocationID' => $LocationID,
                    'Name' => $deviceName->Name,
                );
                Devices::create($data);
            }
        }
        Log::channel('custom_log')->info('Updation:- Location: \'' . $request->LocationName . '\' updated by \'' . $name->UserID . '\' at ' . Carbon::now());
        return redirect('/location-management')->with('success', 'Location Updated Successfully.');

        // return redirect()->back()->with('success', 'Location Updated Successfully.');
    }

    public function saveBrand(Request $request)
    {
        $name = Session::get('userID');
        if ($request->brand_id) {
            DB::table('BeerBrands')->where('BeerBrandsID', $request->brand_id)->update(
                [
                    'BeerTypeID' => $request->beer_type_id,
                    'Brand' => $request->brand_name,
                    'ABV' => $request->abv,
                    'Comments' => $request->comments,
                ]
            );
            Log::channel('custom_log')->info('Updation:- Brand: \'' . $request->brand_name . '\' updated by \'' . $name->UserID . '\' at ' . Carbon::now());
            return redirect('/brand-management')->with('success', 'Brand Updated Successfully.');
            // return response()->json(['success' => 'Data Updated successfully.']);
        }


        $rules = array(
            'brand_name' => 'required|max:64',
            'abv' => 'required',
            'comments' => 'required|max:256',
            'beer_type_id' => 'required',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'BeerTypeID' => $request->beer_type_id,
            'Brand' => $request->brand_name,
            'ABV' => $request->abv,
            'Comments' => $request->comments,

        );

        BeerBrand::create($form_data);

        Log::channel('custom_log')->info('Insertion:- Brand: \'' . $request->brand_name . '\' Added by \'' . $name->UserID . '\' at ' . Carbon::now());
        return redirect('/brand-management')->with('success', 'Brand Added Successfully.');
        // return response()->json(['success' => 'Data Added successfully.']);
    }

    public function saveAlert(Request $request)
    {
        $name = Session::get('userID');
        if ($request->alert_id) {
            DB::table('Alerts')->where('AlertID', $request->alert_id)->update(
                [
                    'AlertName' => $request->alert_name,
                    'AlertDescription' => $request->alert_description,
                ]
            );
            Log::channel('custom_log')->info('Updation:- Alert: \'' . $request->alert_name . '\' updated by \'' . $name->UserID . '\' at ' . Carbon::now());
            return redirect('/alert-management')->with('success', 'Alert Updated Successfully.');
            // return response()->json(['success' => 'Data Updated Successfully.']);
        }

        $rules = array(
            'alert_name' => 'required|max:64',
            'alert_description' => 'required|max:64'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'AlertName' => $request->alert_name,
            'AlertDescription' => $request->alert_description
        );

        AlertCenter::create($form_data);
        Log::channel('custom_log')->info('Insertion:- Alert: \'' . $request->alert_name . '\' Added by \'' . $name->UserID . '\' at ' . Carbon::now());
        return redirect('/alert-management')->with('success', 'Alert Added Successfully.');
        // return response()->json(['success' => 'Data Added successfully.']);
    }

    public function reportApiSensor($brand, $type, $daysfilter, $daysfilter2)
    {    //$brand = explode(",", $brand);

        $daysfilter = str_replace('T', ' ', $daysfilter);
        $daysfilter2 = str_replace('T', ' ', $daysfilter2);

        $dateTime1 = DateTime::createFromFormat('Y-m-d H:i', $daysfilter);
        $dateTime2 = DateTime::createFromFormat('Y-m-d H:i', $daysfilter2);

        $timeDifference = $dateTime2->diff($dateTime1);
        $totalHours = $timeDifference->days * 24 + $timeDifference->h;

        if ($totalHours <= 48) {
            // for <= 48
            $data = DB::table(function ($subquery) use ($brand) {
                $subquery->select([
                    'LineDataSummary.Line AS Line',
                    'DeviceLines.BeerBrandsID AS BeerBrandsID',
                    'LineDataSummary.ReportDateTime',
                    DB::raw('TRUNCATE(SUM(LineDataSummary.Temp) / SUM(LineDataSummary.CNTSamples), 2) AS Temp'),
                    DB::raw('TRUNCATE(SUM(LineDataSummary.Pres) / SUM(LineDataSummary.CNTSamples), 2) AS Pres'),
                    DB::raw('TRUNCATE(SUM(LineDataSummary.TDS) / SUM(LineDataSummary.CNTSamples), 2) AS TDS'),
                    DB::raw('IF(@last = LineDataSummary.Line, @top:=@top+1, @top:=0) AS ztop'),
                    DB::raw('@last:=LineDataSummary.Line AS update_last')
                ])
                    ->from('LineDataSummary')
                    ->join('DeviceLines', function ($join) {
                        $join->on('DeviceLines.Line', '=', 'LineDataSummary.Line')
                            ->on('DeviceLines.DevicesID', '=', 'LineDataSummary.DevicesID');
                    })
                    ->whereIn('LineDataSummary.DevicesID', [$brand])
                    ->groupBy('LineDataSummary.Line', 'LineDataSummary.ReportDateTime', 'DeviceLines.BeerBrandsID')
                    ->orderBy('LineDataSummary.Line', 'asc')
                    ->orderBy('LineDataSummary.ReportDateTime', 'asc');
            }, 't2')
                ->join('BeerBrands', 't2.BeerBrandsID', '=', 'BeerBrands.BeerBrandsID')
                ->select([
                    'Line',
                    'BeerBrands.Brand AS Brand',
                    DB::raw('GROUP_CONCAT(DATE_FORMAT(t2.ReportDateTime, "%Y-%m-%d %H:00")) AS RDT'),
                    DB::raw('GROUP_CONCAT(Temp) AS Temp'),
                    DB::raw('GROUP_CONCAT(Pres) AS Pres'),
                    DB::raw('GROUP_CONCAT(TDS) AS TDS')
                ])
                ->whereRaw('`ReportDateTime` >= ?', [$daysfilter])
                ->whereRaw('`ReportDateTime` < ?', [$daysfilter2])
                ->groupBy('Line', 'Brand')
                ->get();
        } else {
            //daily query

            $data = DB::table(function ($query) use ($brand, $daysfilter, $daysfilter2) {
                $query->select('Line', DB::raw('GROUP_CONCAT(ReportDateTime) AS RDT'), 'BeerBrands.Brand')
                    ->selectRaw('GROUP_CONCAT(Temp) AS Temp')
                    ->selectRaw('GROUP_CONCAT(Pres) AS Pres')
                    ->selectRaw('GROUP_CONCAT(TDS) AS TDS')
                    ->from(function ($subquery) use ($brand) {
                        $subquery->select(
                            'LineDataSummary.Line as Line',
                            'DeviceLines.BeerBrandsID AS BeerBrandsID',
                            DB::raw('SUBSTRING(ReportDateTime, 1, 10) AS ReportDateTime'),
                            DB::raw('TRUNCATE(SUM(Temp) / SUM(CNTSamples), 2) AS Temp'),
                            DB::raw('TRUNCATE(SUM(Pres) / SUM(CNTSamples), 2) AS Pres'),
                            DB::raw('TRUNCATE(SUM(TDS) / SUM(CNTSamples), 2) AS TDS'),
                            DB::raw('@last'),
                            DB::raw('IF(@last = LineDataSummary.Line, @top:=@top+1, @top:=0) AS ztop'),
                            DB::raw('@last:=LineDataSummary.Line AS update_last')
                        )
                            ->from('LineDataSummary')
                            ->join('DeviceLines', function ($join) {
                                $join->on('DeviceLines.Line', '=', 'LineDataSummary.Line')
                                    ->on('DeviceLines.DevicesID', '=', 'LineDataSummary.DevicesID');
                            })
                            ->whereRaw('LineDataSummary.DevicesID IN (?)', [$brand])
                            ->groupBy('LineDataSummary.Line', DB::raw('SUBSTRING(ReportDateTime, 1, 10)'), 'DeviceLines.BeerBrandsID')
                            ->orderBy('LineDataSummary.Line')
                            ->orderBy('ReportDateTime', 'ASC');
                    }, 't2')
                    ->join('BeerBrands', 't2.BeerBrandsID', '=', 'BeerBrands.BeerBrandsID')
                    ->whereRaw('ReportDateTime >= ?', [$daysfilter])
                    ->whereRaw('ReportDateTime < ?', [$daysfilter2])
                    ->groupBy('Line', 'Brand')
                    ->orderBy('RDT', 'ASC');
            })->get();
        }

        $datajson = json_decode($data, true); // Decode the JSON array into an associative array

        if (count($datajson) > 0) {
            $firstObject = $datajson[0]; // Get the first object from the array

            $rdtArray = explode(',', $firstObject['RDT']); // Split the "RDT" string into an array

            return response()->json([
                'status' => 1,
                'data' => $data,
                'type' => $type,
                'dataTime' => $rdtArray,
            ]);
        } else {
            return response()->json([
                'status' => 1,
                'data' => $data,
                'type' => $type,
                'dataTime' => [],
            ]);
        }
    }


    public function LoadAnalysis($brand, $fromdate_span1, $todate_span1, $fromdate_span2, $todate_span2, $type, Request $request)
    {
        //brands
        if ($brand == 'All') {

            $brands = BeerBrand::select('BeerBrands.BeerBrandsID', 'BeerBrands.Brand')
                ->join('DeviceLines', 'BeerBrands.BeerBrandsID', '=', 'DeviceLines.BeerBrandsID')
                ->join('Devices', 'DeviceLines.DevicesID', '=', 'Devices.DevicesID')
                ->where('Devices.LocationID', $request->location)
                ->distinct() // Retrieve distinct beer brands
                ->pluck('BeerBrandsID')->all();
            // $brands = BeerBrand::pluck('BeerBrandsID')->all();
        } else {
            $brands = explode(',', $brand);
        }

        // span 1
        $get_fromdate_span1 = str_replace("T", " ", $fromdate_span1);
        $get_todate_span1 = str_replace("T", " ", $todate_span1);

        $dateTime = new DateTime($get_fromdate_span1);
        $get_fromdate_span1 = $dateTime->format('Y-m-d h:i:s A');

        $dateTime = new DateTime($get_todate_span1);
        $get_todate_span1 = $dateTime->format('Y-m-d h:i:s A');


        // span 2
        $get_fromdate_span2 = str_replace("T", " ", $fromdate_span2);
        $get_todate_span2 = str_replace("T", " ", $todate_span2);

        $dateTime = new DateTime($get_fromdate_span2);
        $get_fromdate_span2 = $dateTime->format('Y-m-d h:i:s A');

        $dateTime = new DateTime($get_todate_span2);
        $get_todate_span2 = $dateTime->format('Y-m-d h:i:s A');


        if ($type == 'ounces') {
            $span1 = DB::table('BeerBrands AS BB')
                ->select(
                    DB::raw('DATE_FORMAT(ReportDateTime,"%m-%d-%Y") AS date'),
                    DB::raw("SUM((Pulse * 1.15) / 29.57) AS Ounces")
                )
                ->join('DeviceLines AS DL', 'BB.BeerBrandsID', '=', 'DL.BeerBrandsID')
                ->join('LineDataSummary AS LDS', function ($join) use ($get_fromdate_span1, $get_todate_span1) {
                    $join->on('DL.DevicesID', '=', 'LDS.DevicesID')
                        ->on('DL.Line', '=', 'LDS.Line')
                        ->whereRaw("ReportDateTime >= '{$get_fromdate_span1}'")
                        ->whereRaw("ReportDateTime < '{$get_todate_span1}'");
                })
                ->whereIn('BB.BeerBrandsID', $brands)
                ->groupBy(DB::raw('DATE_FORMAT(ReportDateTime,"%m-%d-%Y")'))
                ->orderBy(DB::raw('DATE_FORMAT(ReportDateTime,"%m-%d-%Y")'))
                ->get();

            $count = $span1->count();


            $span2 = DB::table('BeerBrands AS BB')
                ->select(
                    DB::raw('DATE_FORMAT(ReportDateTime,"%m-%d-%Y") AS date'),
                    DB::raw("SUM((Pulse * 1.15) / 29.57) AS Ounces")
                )
                ->join('DeviceLines AS DL', 'BB.BeerBrandsID', '=', 'DL.BeerBrandsID')
                ->join('LineDataSummary AS LDS', function ($join) use ($get_fromdate_span2, $get_todate_span2) {
                    $join->on('DL.DevicesID', '=', 'LDS.DevicesID')
                        ->on('DL.Line', '=', 'LDS.Line')
                        ->whereRaw("ReportDateTime >= {$get_fromdate_span2}'")
                        ->whereRaw("ReportDateTime < '{$get_todate_span2}'");
                })
                ->whereIn('BB.BeerBrandsID', $brands)
                ->groupBy(DB::raw('DATE_FORMAT(ReportDateTime,"%m-%d-%Y")'))
                ->orderBy(DB::raw('DATE_FORMAT(ReportDateTime,"%m-%d-%Y")'))
                ->get();


            return response()->json(['span1' => $span1, 'span2' => $span2, 'count' => $count]);
        }

        if ($type == 'pressure') {

            $span1 = DB::table('BeerBrands AS BB')
                ->select(
                    DB::raw('DATE_FORMAT(ReportDateTime,"%m-%d-%Y") AS date'),
                    DB::raw('FORMAT(SUM(LDS.Pres)/SUM(LDS.CNTSamples),"#.00") AS AvgPres')
                )
                ->join('DeviceLines AS DL', 'BB.BeerBrandsID', '=', 'DL.BeerBrandsID')
                ->join('LineDataSummary AS LDS', function ($join) use ($get_fromdate_span1, $get_todate_span1) {
                    $join->on('DL.DevicesID', '=', 'LDS.DevicesID')
                        ->on('DL.Line', '=', 'LDS.Line')
                        ->whereRaw("ReportDateTime >= '{$get_fromdate_span1}'")
                        ->whereRaw("ReportDateTime < '{$get_todate_span1}'");
                })
                ->whereIn('BB.BeerBrandsID', $brands)
                ->groupBy(DB::raw('DATE_FORMAT(ReportDateTime,"%m-%d-%Y")'))
                ->orderBy(DB::raw('DATE_FORMAT(ReportDateTime,"%m-%d-%Y")'))
                ->get();


            $count = $span1->count();

            $span2 = DB::table('BeerBrands AS BB')
                ->select(
                    DB::raw('DATE_FORMAT(ReportDateTime,"%m-%d-%Y") AS date'),
                    DB::raw('FORMAT(SUM(LDS.Pres)/SUM(LDS.CNTSamples),"#.00") AS AvgPres')
                )
                ->join('DeviceLines AS DL', 'BB.BeerBrandsID', '=', 'DL.BeerBrandsID')
                ->join('LineDataSummary AS LDS', function ($join) use ($get_fromdate_span2, $get_todate_span2) {
                    $join->on('DL.DevicesID', '=', 'LDS.DevicesID')
                        ->on('DL.Line', '=', 'LDS.Line')
                        ->whereRaw("ReportDateTime >= '{$get_fromdate_span2}'")
                        ->whereRaw("ReportDateTime < '{$get_todate_span2}'");
                })
                ->whereIn('BB.BeerBrandsID', $brands)
                ->groupBy(DB::raw('DATE_FORMAT(ReportDateTime,"%m-%d-%Y")'))
                ->orderBy(DB::raw('DATE_FORMAT(ReportDateTime,"%m-%d-%Y")'))
                ->get();


            return response()->json(['span1' => $span1, 'span2' => $span2, 'count' => $count]);
        }

        if ($type == 'temperature') {


            $span1 = DB::table('BeerBrands AS BB')
                ->select(
                    DB::raw('DATE_FORMAT(ReportDateTime,"%m-%d-%Y") AS date'),
                    DB::raw('FORMAT(SUM(LDS.Temp)/SUM(LDS.CNTSamples),"#.00") AS AvgTemp')
                )
                ->join('DeviceLines AS DL', 'BB.BeerBrandsID', '=', 'DL.BeerBrandsID')
                ->join('LineDataSummary AS LDS', function ($join) use ($get_fromdate_span1, $get_todate_span1) {
                    $join->on('DL.DevicesID', '=', 'LDS.DevicesID')
                        ->on('DL.Line', '=', 'LDS.Line')
                        ->whereRaw("ReportDateTime >= '{$get_fromdate_span1}'")
                        ->whereRaw("ReportDateTime < '{$get_todate_span1}'");
                })
                ->whereIn('BB.BeerBrandsID', $brands)
                ->groupBy(DB::raw('DATE_FORMAT(ReportDateTime,"%m-%d-%Y")'))
                ->orderBy(DB::raw('DATE_FORMAT(ReportDateTime,"%m-%d-%Y")'))
                ->get();


            $count = $span1->count();

            $span2 = DB::table('BeerBrands AS BB')
                ->select(
                    DB::raw('DATE_FORMAT(ReportDateTime,"%m-%d-%Y") AS date'),
                    DB::raw('FORMAT(SUM(LDS.Temp)/SUM(LDS.CNTSamples),"#.00") AS AvgTemp')
                )
                ->join('DeviceLines AS DL', 'BB.BeerBrandsID', '=', 'DL.BeerBrandsID')
                ->join('LineDataSummary AS LDS', function ($join) use ($get_fromdate_span2, $get_todate_span2) {
                    $join->on('DL.DevicesID', '=', 'LDS.DevicesID')
                        ->on('DL.Line', '=', 'LDS.Line')
                        ->whereRaw("ReportDateTime >= '{$get_fromdate_span2}'")
                        ->whereRaw("ReportDateTime < '{$get_todate_span2}'");
                })
                ->whereIn('BB.BeerBrandsID', $brands)
                ->groupBy(DB::raw('DATE_FORMAT(ReportDateTime,"%m-%d-%Y")'))
                ->orderBy(DB::raw('DATE_FORMAT(ReportDateTime,"%m-%d-%Y")'))
                ->get();


            return response()->json(['span1' => $span1, 'span2' => $span2, 'count' => $count]);
        }
    }

    public function LocationGetAll($id)
    {
        return Devices::where('AccountID', $id)->select('Name', 'DevicesID')->get();
    }

    public function LineDelete($id)
    {
        $result = DB::table('DeviceLines')->where('DeviceLinesID', $id)->delete();

        if ($result) {
            return response()->json([
                'status' => 200,
                'message' => 'Line Deleted Successfully!',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Line not found.',
            ]);
        }
    }
}
