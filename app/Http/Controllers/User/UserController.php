<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AlertCenter;
use App\Models\BeerBrand;
use App\Models\BeerType;
use App\Models\DeviceLine;
use App\Models\Devices;
use App\Models\Distributor;
use App\Models\KegType;
use App\Models\LineData;
use App\Models\Location;
use App\Models\UserAccount;
use App\Models\UserDemoGraphic;
use App\Models\Users\User;
use App\Models\UserSession;
use App\Services\AccountService;
use App\Services\LineDataService;
use App\Services\LocationService;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use OpenApi\Annotations as OA;
use App\Services\AuthService;

class UserController extends Controller
{
    private LineDataService $lineDataService;

    public function __construct(LineDataService $lineDataService)
    {
        $this->lineDataService = $lineDataService;
    }

    public function updateAlertExpiry(Request $request)
    {

        DB::table('Alert_Expiries')
            ->update(
                [
                    'is_true' => 0,
                ]
            );
        DB::table('Alert_Expiries')->where('expire_after', $request->expiry)
            ->update(
                [
                    'is_true' => 1,
                ]
            );
        return response()->json(
            [
                'status' => '1',
            ]
        );
    }

    public function viewregister()
    {
        return view('Auth.register');
    }

    public function register(Request $request)
    {
        # code...
    }

    public function viewlogin()
    {
        return view('Auth.login');
    }

    public function login(Request $request)
    {
        return view('Auth.login');
    }

    public function loginDashboard(Request $request)
    {
        $authService = new AuthService();
        $result = $authService->login($request, false); // Web Login (false)

        if (isset($result['error'])) {
            return  (isset($result['validationError']) && $result['validationError'])
                        ? $result['message']
                        : response()->json(['error' => $result['message']]);
        }

        return response()->json(['success' => 'Login Successfully']);
    }

    public function logout()
    {
        Session::flush();
        return redirect('/')->with('success', 'Logout Successfully');
    }

    /**
     * @OA\Get(
     *     path="/getHomeAlerts/{location_id}",
     *     summary="List all Alerts of given location",
     *     tags={"Alerts"},
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
     *         description="An array of Alerts",
     *         @OA\Schema(ref="#/components/schemas/Alerts"),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getHomeAlerts($location_id): JsonResponse
    {
        $location = Location::find($location_id);
        if (!$location) {
            return response()->json(['error' => 'Location not found']);
        }

        $locationCookie = Cookie::make('user_location', $location->LocationID);

        $alertLists = DB::table('DeviceLinesAlertCurrent')
            ->join('DeviceLines', function ($join) {
                $join->on('DeviceLinesAlertCurrent.DevicesID', '=', 'DeviceLines.DevicesID')
                    ->on('DeviceLinesAlertCurrent.line', '=', 'DeviceLines.Line');
            })
            ->join('BeerBrands', 'DeviceLines.BeerBrandsID', '=', 'BeerBrands.BeerBrandsID')
            ->join('Devices', 'DeviceLinesAlertCurrent.DevicesID', '=', 'Devices.DevicesID')
            ->join('Alerts', 'DeviceLinesAlertCurrent.AlertID', '=', 'Alerts.AlertID')
            ->join('Location', 'Devices.LocationID', '=', 'Location.LocationID')
            ->select('DeviceLinesAlertCurrent.*', 'BeerBrands.*', 'Alerts.*', 'Devices.*', 'Location.LocationName', 'Location.TimeZone')
            ->where('DeviceLinesAlertCurrent.RecordStatus', '=', 1)
            ->where('DeviceLinesAlertCurrent.AckDateTime', '=', '0000-00-00 00:00:00')
            ->where('Devices.RecordStatus', '!=', 2)
            ->where('Devices.LocationID', $location->LocationID)
            ->limit(2)
            ->orderBy('DeviceLinesAlertCurrent.AlertDateTime', 'desc')
            ->get();

        foreach ($alertLists as $alertList) {
            $dateTime = new DateTime($alertList->AlertDateTime);
            $timeInAmPm = $dateTime->format('m-d-y h:i A');
            $timeForPour = $dateTime->format('m/d/Y H:i:s');
            $alertList->AlertDateTime = $timeInAmPm;
            $alertList->timeForPour = $timeForPour;

            $alertList->describe = $this->getAlertDescription($alertList);
        }

        return response()->json($alertLists)->withCookie($locationCookie);
    }

    /**
     * @param mixed $alertList
     * @return string
     */
    public function getAlertDescription(mixed $alertList): string
    {
        if ($alertList->AlertID == 1) {
            return $alertList->OptTemp <= $alertList->TempAlertValue ? "HIGH Temperature" : "LOW Temperature";
        } elseif ($alertList->AlertID == 2) {
            return $alertList->OptPressure <= $alertList->PressAlertValue ? "HIGH Pressure" : "LOW Pressure";
        } elseif ($alertList->AlertID == 3) {
            return "After Hour Pouring";
        } else {
            return $alertList->AlertDescription;
        }
    }

    public function home(Request $request)
    {
        $alertQuery = DB::table('DeviceLinesAlertCurrent')
            ->join('BeerBrands', 'DeviceLinesAlertCurrent.BeerBrandID', '=', 'BeerBrands.BeerBrandsID')
            ->join('Devices', 'DeviceLinesAlertCurrent.DevicesID', '=', 'Devices.DevicesID')
            ->join('Alerts', 'DeviceLinesAlertCurrent.AlertID', '=', 'Alerts.AlertID')
            ->where('DeviceLinesAlertCurrent.RecordStatus', '=', '1')
            ->where('Devices.RecordStatus', '!=', 2)
            ->orderBy('DeviceLinesAlertCurrent.AlertDateTime', 'desc');

        if ($request->location) {
            $alertQuery->where('Devices.LocationID', $request->location)
                ->limit(2)->get();
            return $alertQuery;
        }

        $alertList = $alertQuery
            // ->orderBy('DeviceLinesAlertCurrent.DeviceLinesAlertID', 'desc')
            // ->where('DeviceLinesAlert.AckDateTime', '')
            // ->select('*', 'DeviceLinesAlertCurrent.DeviceLinesAlertID as IDAck')
            ->limit(2)->get();

        $locationsUsers = LocationService::LocationDisplay();

        return view('home', compact('alertList', 'locationsUsers'));
    }

    public function pourScoreDetailReport()
    {
        $locationsUsers = LocationService::LocationDisplay();

        return view('UserReporting.pourscoreDetail', compact('locationsUsers'));
    }

    /**
     * @OA\Get(
     *     path="/load/line/data/{device_id}/{from_date}/{to_date}/{page_number}",
     *     summary="Get Line wise summary for home page",
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
     *     @OA\Parameter(
     *          name="page_number",
     *          in="path",
     *          description="Page Number (Default 1)",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int32",
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="An array of LineData",
     *         @OA\Schema(ref="#/components/schemas/LineData"),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function LoadLineDate($device, $fromDate, $toDate, $pageNumber): JsonResponse
    {
        if ($pageNumber == 1) {
            $skip = 0;
        }
        if ($pageNumber > 1) {
            $skip = (($pageNumber * 10) - 10);
        }

        $fromDate = str_replace("T", " ", $fromDate);
        $endDate = str_replace("T", " ", $toDate);

        $lineDataItems = $this->lineDataService->getFromLineData($device, $fromDate, $endDate);

        $finalArray = $lineDataItems;
        $total = $lineDataItems->count();
        $from = $skip + 1;
        $to = $skip + 10;
        $lastPage = ceil($total / 10);

        return response()->json([
            'result' => $finalArray, 'total' => $total, 'from' => $from, 'to' => $to,
            'lastPage' => $lastPage, 'formattedFromDate' => $fromDate, 'formattedToDate' => $toDate
        ]);
    }

    /**
     * @OA\Get(
     *     path="/load/devices/{location_id}",
     *     summary="List all Devices for a location",
     *     tags={"Devices"},
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
     *         description="An array of Devices",
     *         @OA\Schema(ref="#/components/schemas/Devices"),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function loadDevices($location_id)
    {
        return Devices::select(['DevicesID', 'Name', 'Serial'])->where('LocationID', $location_id)->where('RecordStatus', '!=', 2)->get();
    }

    public function FloteqUserAdmin()
    {
        $name = Session::get('userID');
        $useAccount = UserAccount::where('UserID', $name->UserID)->first();

        //    return DB::table('Accounts')
        //    ->join('UserAccount','Accounts.AccountID','=','UserAccount.AccountID')
        //    ->join('UserDemographic','UserAccount.UserID','UserDemographic.UserID')
        //    ->join('User','UserDemographic.UserID','User.UserID')
        //    ->orWhere('UserAccount.ConfigurationID', '!=', 9999)
        //    ->select('User.UserID')
        //    ->get();

        $users = DB::table('Accounts as e')
            ->join('UserAccount as f', function ($join) {
                $join->on('f.AccountID', '=', 'e.AccountID')
                    ->orWhere('e.ConfigurationID', '=', 9999);
            })
            ->join('User as a', 'a.UserID', '=', 'f.UserID')
            ->join('UserDemographic as c', 'c.UserID', '=', 'a.UserID')
            ->join('Location as d', 'd.UserID', '=', 'f.UserID')
            ->select(
                'e.AccountID',
                'f.AdminAccess',
                'a.UserID',
                'a.Email as Email',
                'c.FirstName',
                'c.LastName',
                // 'd.*',
                'f.IsActive as IsActive'
            )
            ->where('e.AccountID', '=', $useAccount->AccountID)
            ->orderBy('f.AccountID')
            ->orderBy('c.FirstName')
            ->orderBy('c.LastName')
            ->get();

        return view('Admins.index', compact('users'));
    }

    public function LineManagement()
    {
        $data = DeviceLine::orderBy("Line")->get();
        $locations = Location::orderBy("LocationName")->get();
        $lastLocation = Location::latest()->first();
        $devices = Devices::where('LocationID', $lastLocation->LocationID)->orderBy("Name")->get();
        $locationsUsers = LocationService::LocationDisplay();

        return view('UserManagement.index', compact('data', 'locations', 'devices', 'locationsUsers'));
    }

    public function AlertCenter(Request $request): Factory|View|Application
    {
        $name = Session::get('userID');
        $userID = $name->UserID;
        $configurationID = 9999;
        $locationIds = DB::table('Location AS l')
            ->select('l.LocationID')
            ->distinct()
            ->join('Accounts AS a', 'a.AccountID', '=', 'l.UserID')
            ->join('UserAccount AS ua', function ($join) use ($configurationID) {
                $join->on('ua.AccountID', '=', 'a.AccountID')
                    ->on('ua.LocationID', '=', 'l.LocationID')
                    ->orWhere('ua.ConfigurationID', '=', $configurationID);
            })
            ->join('Devices AS d', function ($join) {
                $join->on('d.AccountID', '=', 'l.UserID')
                    ->on('d.LocationID', '=', 'l.LocationID');
            })
            ->where('l.LocationType', '=', 1)
            ->where('ua.UserID', '=', $userID)
            // ->orderBy('l.LocationName')
            ->pluck('l.LocationID')
            ->toArray();

        $names = Session::get('userID');
        $name = DB::table('UserDemographic')
            ->where('UserID', $names->UserID)
            ->first();
        $userLocation = $request->cookie('user_location');
        if ($userLocation) {
            $locationIds = $userLocation;
        } else {
            $locationIds = $name->LocationID;
        }
        $locationId = $request->input('location_id');

        $locationsUsers = LocationService::LocationDisplay();

        $alert = DB::table('Alerts')->get();
        $expires = DB::table('Alert_Expiries')->get();
        $alerts = DB::table('DeviceLinesAlertCurrent')
            ->join('DeviceLines', function ($join) {
                $join->on('DeviceLinesAlertCurrent.DevicesID', '=', 'DeviceLines.DevicesID')
                    ->on('DeviceLinesAlertCurrent.line', '=', 'DeviceLines.line');
            })
            ->join('BeerBrands', 'DeviceLines.BeerBrandsID', '=', 'BeerBrands.BeerBrandsID')
            ->join('Devices', 'DeviceLinesAlertCurrent.DevicesID', '=', 'Devices.DevicesID')
            ->join('Alerts', 'DeviceLinesAlertCurrent.AlertID', '=', 'Alerts.AlertID')
            ->join('UserDemographic', 'DeviceLinesAlertCurrent.UserAccountID', 'UserDemographic.UserID', 'UserDemographic.*')
            ->select('DeviceLinesAlertCurrent.*', 'BeerBrands.*', 'Alerts.*', 'Devices.*')
            ->select('DeviceLinesAlertCurrent.*', 'BeerBrands.*', 'Alerts.*', 'Devices.*')
            ->where('DeviceLinesAlertCurrent.RecordStatus', '=', '1')
            ->where('DeviceLinesAlertCurrent.AckDateTime', '0000-00-00 00:00:00')
            // ->orderBy('DeviceLinesAlert.DeviceLinesAlertID', 'desc')
            ->select('*', 'UserDemographic.FirstName as UserACK')
            ->orderBy('DeviceLinesAlertCurrent.AlertDateTime', 'desc')
            ->take(20)->get();

        $alertList = DB::table('DeviceLinesAlertCurrent')
            ->join('DeviceLines', function ($join) {
                $join->on('DeviceLinesAlertCurrent.DevicesID', '=', 'DeviceLines.DevicesID')
                    ->on('DeviceLinesAlertCurrent.line', '=', 'DeviceLines.line');
            })
            ->join('BeerBrands', 'DeviceLinesAlertCurrent.BeerBrandID', '=', 'BeerBrands.BeerBrandsID')
            ->join('Devices', 'DeviceLinesAlertCurrent.DevicesID', '=', 'Devices.DevicesID')
            ->join('Alerts', 'DeviceLinesAlertCurrent.AlertID', '=', 'Alerts.AlertID')
            ->join('Location', 'Devices.LocationID', '=', 'Location.LocationID')
            ->select('DeviceLinesAlertCurrent.*', 'BeerBrands.*', 'Alerts.*', 'Devices.*', 'Location.LocationName')
            ->where('DeviceLinesAlertCurrent.RecordStatus', '=', '1')
            ->where('DeviceLinesAlertCurrent.AckDateTime', '0000-00-00 00:00:00')
            ->when($locationId, function ($query) use ($locationId) {
                return $query->where('Devices.LocationID', $locationId);
            }, function ($query) use ($locationIds) {
                return $query->where('Devices.LocationID', $locationIds);
            })
            ->orderBy('DeviceLinesAlertCurrent.AlertDateTime', 'desc')
            ->take(4)
            ->get();

        foreach ($alertList as $alertItem) {
            $alertItem->describe = $this->getAlertDescription($alertItem);
        }

        return view('AlertCenter.index', compact('alertList', 'alert', 'alerts', 'locationsUsers', 'expires', 'locationIds'));
    }

    public function Documentation()
    {
        return view('Documentation.index');
    }

    public function documentationUsage()
    {
        return view('Documentation.Usage');
    }

    // User reporting

    // Sensor reporting

    public function documentationPolicy()
    {
        return view('Documentation.Policy');
    }

    public function documentationSaas()
    {
        return view('Documentation.Saas');
    }

    public function SensorReporting()
    {
        $brands = BeerBrand::all();
        //        $lineData = LineData::all();
        $lineData = LineData::orderBy("LineDataID", "DESC")->take(20)->get();
        $devices = Devices::all();
        $lastDevice = Devices::latest()->first();
        $allDevicesIds = Devices::pluck('DevicesID')->all();
        $locations = Location::all();

        return view('UserReporting.index', compact('brands', 'lineData', 'devices', 'lastDevice', 'allDevicesIds', 'locations'));
    }

    public function LineReporting()
    {
        $brands = BeerBrand::all();
        $lineData = LineData::orderBy("LineDataID", "DESC")->take(20)->get();
        $devices = Devices::all();
        $lastDevice = Devices::latest()->first();
        $allDevicesIds = Devices::pluck('DevicesID')->all();
        $locations = Location::all();
        $locationsUsers = LocationService::LocationDisplay();

        return view('UserReporting.linesensor', compact('brands', 'lineData', 'devices', 'lastDevice', 'allDevicesIds', 'locations', 'locationsUsers'));
    }

    public function LineReporting2()
    {
        $brands = BeerBrand::all();
        $lineData = LineData::orderBy("LineDataID", "DESC")->take(20)->get();
        $devices = Devices::all();
        $lastDevice = Devices::latest()->first();
        $allDevicesIds = Devices::pluck('DevicesID')->all();
        $locations = Location::all();

        return view('UserReporting.linesensorPres', compact('brands', 'lineData', 'devices', 'lastDevice', 'allDevicesIds', 'locations'));
    }

    public function LineReporting3()
    {
        $brands = BeerBrand::all();
        $lineData = LineData::orderBy("LineDataID", "DESC")->take(20)->get();
        $devices = Devices::all();
        $lastDevice = Devices::latest()->first();
        $allDevicesIds = Devices::pluck('DevicesID')->all();
        $locations = Location::all();

        return view('UserReporting.linesensorTDS', compact('brands', 'lineData', 'devices', 'lastDevice', 'allDevicesIds', 'locations'));
    }

    public function BrandComparison()
    {
        $locationsID = Location::orderBy('LocationID', 'DESC')->get();
        $locations = BeerBrand::all();
        $devices = Devices::all();
        $locationsUsers = LocationService::LocationDisplay();
        return view('UserReporting.brandcomparison', compact('locations', 'locationsID', 'devices', 'locationsUsers'));
    }

    public function PourscoreReport()
    {
        // $locations = Location::all();
        $locations = LocationService::LocationDisplay();
        return view('UserReporting.pourscore', compact('locations'));
    }

    public function TrendAnalysis()
    {
        $brands = BeerBrand::all();
        $locationsID = Location::orderBy('LocationID', 'DESC')->get();

        // last 7 days
        $date = new DateTime();
        $span1End = $date->format('Y-m-d 06:00');
        // last 14 days
        $date2 = new DateTime('7 days ago');
        $span1Start = $date2->format('Y-m-d 06:00');
        //last 8 days
        $date3 = new DateTime('7 days ago');
        $span2End = $date3->format('Y-m-d 06:00');
        $date4 = new DateTime('14 days ago');

        $span2Start = $date4->format('Y-m-d 06:00');
        $locationsUsers = LocationService::LocationDisplay();


        return view('UserReporting.trendanalysis', compact('locationsID', 'brands', 'span1Start', 'span1End', 'span2Start', 'span2End', 'locationsUsers'));
    }

    /*  public function saveUser(Request $request)
     {
  //       return $request->all();


         if ($request->record_status) {
             $record_status = 1;
         } else {
             $record_status = 0;
         }

         if ($request->allow_login == "on") {
             $access = 1;
         } else {
             $access = 0;
         }

         $request->validate([
             'email' => 'required|unique:User,email',
             'password' => 'required'
         ]);


         $id = DB::table('User')->insertGetId(
             [
                 'Email' => $request->email,
                 'Password' => Hash::make($request->password)
             ]
         );


         if ($id) {
        try {
      $userId = User::where('UserID', $id)->pluck('UserID')->first();

             $user_demographic = array(
                 'UserID' => $userId,
                 'FirstName' => $request->first_name,
                 'LastName' => $request->last_name,
                 'LocationID' => $request->location,
                 'AllowLogin' => $record_status,
                 'RecordStatus' => '1',
             );
             $userDemography = UserDemoGraphic::create($user_demographic);
             $locationString = $request->location1;
             $json = json_encode($locationString);

             foreach ($locationString as $item) {
                 $accounts = array(
                     'LocationID' => $item,
                     'AccountName' => $request->first_name . " " . $request->middle_name . " " . $request->last_name,
                     'ConfigurationID' => '0',
                     'AccountLocationID' => '1',
                     'IsActive' => '1',
                     'RecordStatus' => '1',
                 );
                 $acc = Account::create($accounts);

                 $user_account = array(
                     'UserAccountID' => $acc->id,
                     'UserID' => $userId,
                     'AccountID' => $acc->id,
                     'LocationID' => $item,
                     'ConfigurationID' => '1',
                     'AdminAccess' =>  $access,
                     'IsActive' => $record_status,
                     'RecordStatus' => '1'
                 );
                 UserAccount::create($user_account);
             }


             if ($userDemography) {
                 return redirect()->back()->with('message', 'User Created Successfully !');
             } else {
                 $newUser = User::find($id);
                 $newUser->delete();
             }
}catch(Exception $e) {
        // Handle the exception (e.g., log the error, display an error message)
        return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
    }
         }
     }
*/

    public function saveUser(Request $request)
    {
        User::Validation($request);
        return User::AddUser($request);
    }

    public function addUser()
    {
        $name = Session::get('userID');
        $locations = Location::orderBy('LocationName')->get();
        $accounts = AccountService::AccountsDisplay($name->UserID);
        $locationsUsers = DB::table('Location')
            // ->join('Accounts', 'Accounts.AccountID', '=', 'Location.UserID')
            ->where('Location.LocationType', 1)->orderBy('Location.LocationName')->get();
        // $locationsUsers =  LocationService::LocationDisplay();

        return view('UserManagement.useradd', compact('locations', 'accounts', 'locationsUsers'));
    }

    // public function updateUser(Request $request)
    // {
    //     $accountDelete = UserAccount::where('UserID', $request->user_id)->get();
    //     if ($accountDelete) {
    //         foreach ($accountDelete as $item) {
    //             Account::where('AccountID', $item->AccountID)->update(['IsActive' => '0']);
    //             UserAccount::where('UserAccountID', $item->UserAccountID)->update(['IsActive' => '0']);
    //         }
    //     }

    //     if ($request->allow_login) {
    //         $allow_login = 1;
    //     } else {
    //         $allow_login = 0;
    //     }

    //     if (isset($request->password)) {
    //         DB::table('User')
    //             ->where('UserID', $request->user_id)
    //             ->update([
    //                 'Password' => Hash::make($request->password),
    //                 'AllowLogin' => $allow_login
    //             ]);
    //     }

    //     DB::table('UserDemographic')
    //         ->where('UserID', $request->user_id)
    //         ->update([
    //             'FirstName' => $request->first_name,
    //             'LastName' => $request->last_name,
    //             'LocationID' => $request->location,
    //             'RecordStatus' => "1"
    //         ]);

    //     $locationString = $request->location1;
    //     $json = json_encode($locationString);

    //     foreach ($locationString as $item) {
    //         $accounts = array(
    //             'LocationID' => $item,
    //             'AccountName' => $request->first_name . " " . $request->middle_name . " " . $request->last_name,
    //             'ConfigurationID' => '0',
    //             'AccountLocationID' => '1',
    //             'IsActive' => '1',
    //             'RecordStatus' => '1',
    //         );
    //         $acc = Account::create($accounts);

    //         $user_account = array(
    //             'UserAccountID' => $acc->id,
    //             'UserID' => $request->user_id,
    //             'AccountID' => $acc->id,
    //             'LocationID' => $item,
    //             'ConfigurationID' => '1',
    //             'AdminAccess' =>  $allow_login,
    //             'IsActive' => '1',
    //             'RecordStatus' => '1'
    //         );
    //         UserAccount::create($user_account);
    //     }

    //     return redirect()->back()->with('message', 'User Updated Successfully !');
    // }

    public function AccountEdit($id)
    {
        $data = User::where('User.UserID', $id)->join('UserAccount', 'User.UserID', '=', 'UserAccount.UserID')->first();
        $demographics = UserDemoGraphic::where('UserID', $data->UserID)->first();
        $locations = Location::orderBy('LocationName')->get();
        $location = Location::where('LocationID', $demographics->LocationID)->first();
        $phone = Location::where('userID', $id)->first();
        //    return $accounts = Account::join('UserAccount', 'Accounts.AccountID', '=', 'UserAccount.AccountID')->get();
        $accounts = AccountService::AccountsDisplay($id);

        $userAccont = UserAccount::where('UserID', $id)->where('IsActive', '1')->first();
        $name = Session::get('userID');

        $locationsUsers = DB::table('Location')
            // ->join('Accounts', 'Accounts.AccountID', '=', 'Location.UserID')
            ->where('Location.LocationType', 1)->orderBy('Location.LocationName')->get();
        // $locationsUsers = LocationService::LocationDisplay();

        return view('UserManagement.useredit', compact('data', 'phone', 'demographics', 'locations', 'location', 'accounts', 'userAccont', 'id', 'name', 'locationsUsers'));
    }

    public function AccountDelete($id)
    {
        $name = Session::get('userID');
        if ($id == $name->UserID) {
            return response()->json(
                [
                    'status' => 401,
                    'message' => 'Your can not delete your own account !',
                ]
            );
        }
        DB::table('UserSession')->where('UserID', $id)->delete();
        DB::table('SubAccounts')->where('SubAccounts.UserID', $id)->delete();
        DB::table('UserAccount')->where('UserAccount.UserID', $id)->delete();
        DB::table('User')->where('UserID', $id)->delete();
        DB::table('Accounts')
            ->whereIn('AccountID', function ($query) use ($id) {
                $query->select('AccountID')
                    ->from('UserAccount')
                    ->where('UserAccount.UserID', '=', $id);
            })->delete();

        return response()->json(
            [
                'status' => 200,
                'message' => 'User Deleted Successfully !',
            ]
        );
    }

    public function updateUser(Request $request)
    {
        $locationString = explode(',', $request->location1);
        if (!(in_array($request->location, $locationString) || $request->location1 == $request->location)) {
            return response()->json(['status' => 'notexists', 'message' => 'Default Location does not exist in Selected Location.']);
        }
        $name = Session::get('userID');
        try {
            DB::beginTransaction();

            $allow_login = ($request->allow_login == 'true') ? 1 : 0;
            $record_status = ($request->record_status == 'true') ? 1 : 0;

            $user_id = $request->user_id;
            $user = DB::table('User')->where('UserID', $user_id);
            if (isset($request->password)) {
                $user->update([
                    'Password' => Hash::make($request->password),
                    'AllowLogin' => $allow_login
                ]);
            }

            if (isset($request->email)) {
                $user->update([
                    'Email' => $request->email,
                ]);
            }

            $name = Session::get('userID');
            $userAccount = UserAccount::where('UserID', $name->UserID)->first();
            UserAccount::where('UserID', $request->user_id)->delete();
            $locationS = '111';
            $locationString = explode(',', $request->location1);

            if (in_array($locationS, $locationString) || $request->location1 == $locationS) {
                $user_account = array(
                    'UserID' => $request->user_id,
                    'AccountID' => '42',
                    'LocationID' => '111',
                    'ConfigurationID' => '9999',
                    'AdminAccess' => $allow_login,
                    'IsActive' => $record_status,
                    'RecordStatus' => '1',
                    'InsertDateTime' => date('Y-m-d H:i:s')
                );
                UserAccount::create($user_account);
            } else {
                if (isset($request->location1)) {
                    foreach ($locationString as $item) {
                        // $userID = DB::table('Location')->where('LocationID', $item)->value('UserID');
                        $userID = Account::select('AccountID')->where('LocationID', $item)->first();

                        $result = Location::select('UserID as AccountID', 'LocationID', 'LocationName')
                            ->where('LocationType', 1)
                            ->where('LocationID', $item)
                            ->orderBy('LocationName')
                            ->get();
                        $user_account = array(
                            'UserID' => $request->user_id,
                            'AccountID' => $result[0]->AccountID,
                            'LocationID' => $item,
                            'ConfigurationID' => '1',
                            'AdminAccess' => $allow_login,
                            'IsActive' => $record_status,
                            'RecordStatus' => '1',
                            'InsertDateTime' => date('Y-m-d H:i:s')
                        );
                        UserAccount::create($user_account);
                    }
                }
            }

            if (isset($request->location1)) {
                UserDemoGraphic::where('UserID', $user_id)->delete();
                Location::where('UserID', $user_id)->delete();

                foreach ($locationString as $item) {
                    $user_demographic = [
                        'UserID' => $user_id,
                        'FirstName' => $request->first_name,
                        'LastName' => $request->last_name,
                        'PhonePrimary' => $request->phone,
                        'LocationID' => $item,
                        'AllowLogin' => '1',
                        'RecordStatus' => '1',
                    ];
                    UserDemoGraphic::create($user_demographic);

                    $locationName = Location::where('LocationID', $item)->first();

                    $locationAdd = [
                        'LocationType' => '2',
                        'UserType' => '0',
                        'UserID' => $user_id,
                        'PhonePrimary' => $request->phone,
                        'LocationName' => $locationName->LocationName ?? '',
                    ];
                    Location::create($locationAdd);
                }
            }

            DB::commit();

            Log::channel('custom_log')->info('Updation:- User: \'' . $request->first_name . ' ' . $request->last_name . '\' updated by \'' . $name->UserID . '\' at ' . Carbon::now());
            return response()->json(['status' => 'success', 'message' => 'User Updated Successfully !']);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'An error occurred while updating the user.' . $e]);
        }
    }

    public function CheckLine(Request $request)
    {
        $existingDeviceLine = DB::table('DeviceLines')
            ->where('Line', $request->line)
            ->where('DevicesID', $request->deviceID)
            ->first();

        if ($existingDeviceLine) {
            return response()->json("exist");
        } else {
            return response()->json("ok");
        }
    }

    public function addlinemanagement($selectedLocation, $selectedDevice)
    {
        $name = Session::get('userID');
        $locations = LocationService::LocationDisplay();
        // $locations =  DB::table('UserAccount as b')
        //     ->select('c.LocationID', 'c.LocationName')
        //     ->join('Accounts as a', function ($join) {
        //         $join->on('b.ConfigurationID', '=', DB::raw('9999'))->orOn('a.AccountID', '=', 'b.AccountID');
        //     })
        //     ->join('Location as c', 'c.UserID', '=', 'a.AccountID')
        //     ->where('b.UserID', $name->UserID)
        //     ->where('c.LocationType', 1)
        //     ->orderBy('c.LocationName')
        //     ->get();
        $brands = BeerBrand::all()->sortBy('Brand');
        $kegtypes = KegType::all()->sortBy('KeyName');
        $distributers = Distributor::orderBy('DistName')->get();

        $lastLocation = Location::latest()->where('RecordStatus', '!=', 2)->first();

        $devices = Devices::where('LocationID', $lastLocation->LocationID)->get();

        $locationsUsers = LocationService::LocationDisplay();

        return view('linemanagement.addline', compact('locations', 'brands', 'kegtypes', 'distributers', 'devices', 'locationsUsers', 'selectedLocation', 'selectedDevice'));
    }

    public function addLocation(Request $request)
    {
        $accounts = DB::table('Accounts')
            ->join('Location', 'Location.LocationID', '=', 'Accounts.LocationID')
            ->select('Accounts.*', 'Location.EmailTechnical', 'Location.City', 'Location.State')
            ->get();
        $beerBrand = Devices::all()->where('RecordStatus', '!=', 2);
        return view('LocationManagement.add', compact('beerBrand', 'accounts'));
    }

    public function addBrand(Request $request)
    {
        $beertypes = BeerType::orderBy('Description')->get();
        return view('BrandManagement.add', compact('beertypes'));
    }

    public function editBrand($id)
    {
        $beertypes1 = BeerBrand::where('BeerBrandsID', $id)->first();
        $beertypes = DB::table('BeerType')->get();
        return view('BrandManagement.edit', compact('beertypes', 'beertypes1'));
    }

    public function addAlert(Request $request)
    {
        return view('AlertManagement.add');
    }

    public function editAlert($id)
    {
        $alert = AlertCenter::where('AlertID', $id)->first();
        return view('AlertManagement.edit', compact('alert'));
    }

    // location management

    public function editlinemanagement($devicelinesid)
    {

        $locations = Location::all();
        $brands = BeerBrand::all();
        $kegtypes = KegType::all();
        $distributers = Distributor::all();

        $data = DeviceLine::where("DeviceLinesID", $devicelinesid)->first();
        $deviceLine = DeviceLine::where("DevicesID", $data->DevicesID)->get();


        $devices = Devices::all();


        $selectedLocation = Devices::where("DevicesID", $data->DevicesID)->pluck("LocationID")->first();
        $selectedBrand = $data->BeerBrandsID;
        $selectedKeg = $data->KegTypeID;

        $selectedDistributer = $data->DistAccountID;

        $lineData = LineData::where("DevicesID", $data->DevicesID)->first();

        $locationND = DeviceLine::join('Devices', 'DeviceLines.DevicesID', '=', 'Devices.DevicesID')->where('DeviceLinesID', $devicelinesid)->first();
        $locationNDDD = Location::where('LocationID', $locationND->LocationID)->first();


        return view('linemanagement.editline', compact(
            'data',
            'locations',
            'selectedLocation',
            'brands',
            'kegtypes',
            'distributers',
            'devices',
            'selectedBrand',
            'selectedKeg',
            'selectedDistributer',
            'lineData',
            'locationND',
            'locationNDDD',
            'deviceLine'
        ));
    }

    // brand management

    public function editLocation($locationid)
    {
        $location = Location::where("LocationID", $locationid)->first();
        $deviceLocaton = Devices::where('LocationID', $locationid)->get();
        $beerBrand = Devices::all();
        $accounts = DB::table('Accounts')
            ->join('Location', 'Location.LocationID', '=', 'Accounts.LocationID')
            ->select('Accounts.*', 'Location.EmailTechnical', 'Location.City', 'Location.State')
            ->get();
        return view('LocationManagement.edit', compact('location', 'beerBrand', 'locationid', 'accounts'));
    }

    // alert management

    public function deleteLocation($locationid)
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            // Perform the deletion operation

            // Example: Delete a record from the "users" table


            $location = Location::where("LocationID", $locationid)->delete();

            // Perform other necessary deletions

        } catch (Exception $e) {
        }

        // Enable foreign key constraint checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        return redirect()->back()->with('success', 'Location Deleted Successfully !');
    }

    public function LocationManagement(Request $request)
    {
        $name = Session::get('userID');
        // echo $name->UserID."--";
        // $locations = DB::table('UserAccount as ua')
        //     ->select('l.*', 'a.AccountID','a.AccountName as AccountName')
        //     ->distinct()
        //     ->join('Accounts as a', function ($join) {
        //         $join->on('a.AccountID', '=', 'ua.AccountID')
        //             ->orWhere('ua.ConfigurationID', '=', 9999);
        //     })
        //     ->join('Location as l', 'l.LocationID', '=', 'a.LocationID')
        //     ->where('ua.UserID', $name->UserID)
        //  ->where('l.LocationType',1)
        // ->orderBy('l.LocationName')
        // ->get();
        $locations = DB::table('Location')
            ->select('Location.*', 'AccountName')
            ->join('Accounts', 'Accounts.AccountID', '=', 'Location.UserID')
            ->where('Location.LocationType', 1)->orderBy('Location.LocationName')->get();

        return view('LocationManagement.index', compact('locations'));
    }

    public function BrandManagement(Request $request)
    {
        $brands = BeerBrand::with('beertypes')->get();
        $sortedBrands = $brands->sortBy('Brand');

        return view('BrandManagement.index', compact('sortedBrands'));
    }

    public function AlertManagement(Request $request)
    {
        $alerts = AlertCenter::all();
        return view('AlertManagement.index', compact('alerts'));
    }

    public function ChangeLocation(Request $request)
    {
        if ($request->id == 'LocationDevices') {
            return Devices::where('AccountID', $request->LocationID)->where('RecordStatus', '!=', 2)->get();
        }
        if ($request->id == 'beerbrand') {
            return DB::table('DeviceLines')->where('DevicesID', $request->beerbrand)->select('Line')->get()->unique('Line');
        }
        if ($request->id == 'beerbrandName') {
            return Location::all();
        }
    }

    public function AckData(Request $request)
    {
        $timeString = $request->date;
        $formattedTime = date('Y-m-d H:i:s', strtotime($timeString));
        $name = Session::get('userID');
        $alertCurrent = DB::table('DeviceLinesAlertCurrent')->where('DeviceLinesAlertCurrentID', $request->DeviceLinesAlertCurrentID)->first();
        if ($alertCurrent) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::table('DeviceLinesAlert')->insert([
                'DeviceLinesAlertStatus' => $alertCurrent->DeviceLinesAlertStatus,
                'AccountID' => $alertCurrent->AccountID,
                'DevicesID' => $alertCurrent->DevicesID,
                'UserAccountID' => $name->UserID,
                'AlertID' => $alertCurrent->AlertID,
                'Line' => $alertCurrent->Line,
                'DeviceStatus' => $alertCurrent->DeviceStatus,
                'BeerBrandID' => $alertCurrent->BeerBrandID,
                'KegTypeID' => $alertCurrent->KegTypeID,
                'DistributorID' => $alertCurrent->DistributorID,
                'OptTemp' => $alertCurrent->OptTemp,
                'OptPressure' => $alertCurrent->OptPressure,
                'TempPressAlert' => $alertCurrent->TempPressAlert,
                'TempPressAlertTimeOut' => $alertCurrent->TempPressAlertTimeOut,
                'TempAlertValue' => $alertCurrent->TempAlertValue,
                'PressAlertValue' => $alertCurrent->PressAlertValue,
                'KegCost' => $alertCurrent->KegCost,
                'LineLength' => $alertCurrent->LineLength,
                'LineType' => $alertCurrent->LineType,
                'AlertDateTime' => $alertCurrent->AlertDateTime,
                'AckDateTime' => $formattedTime,
                'RecordStatus' => $alertCurrent->RecordStatus,
                'min_value' => $alertCurrent->min_value,
                'max_value' => $alertCurrent->max_value,
                'AlertCNT' => $alertCurrent->AlertCNT,
                'InsertDateTime' => $alertCurrent->InsertDateTime,
                'UpdateDateTime' => $alertCurrent->UpdateDateTime,

            ]);
            DB::table('DeviceLinesAlertCurrent')->where('DeviceLinesAlertCurrentID', $request->DeviceLinesAlertCurrentID)
                ->delete();
            return response()->json(
                [
                    'status' => '1',
                ]
            );
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    public function AlertData(Request $request)
    {
        $perPage = 20;
        $fromdate = str_replace("T", " ", $request->fromdate);
        $todate = str_replace("T", " ", $request->todate);
        $names = Session::get('userID');
        $name = DB::table('UserDemographic')
            ->where('UserID', $names->UserID)
            ->first();
        $userLocation = $request->cookie('user_location');
        if ($userLocation) {
            $locationId = $userLocation;
        } else {
            $locationId = $name->LocationID;
        }

        //change time and dateformat to be AM PM
        $fromdate = new DateTime($fromdate);
        $fromdate = $fromdate->format('Y-m-d H:i:s');

        $todate = new DateTime($todate);
        $todate = $todate->format('Y-m-d H:i:s');
        $adjustedFromDate = date('Y-m-d H:i:s', strtotime($fromdate));
        $adjustedToDate = date('Y-m-d H:i:s', strtotime($todate));

        if ($request->alertType != 3) {
            $alerts = DB::table('DeviceLinesAlert as DLA')
                ->select(
                    DB::raw("DATE_FORMAT(DLA.AckDateTime, '%m-%d-%y %h:%i %p') as AckDateTime"),
                    DB::raw("DATE_FORMAT(DLA.AlertDateTime, '%m-%d-%y %h:%i %p') as AlertDateTime"),
                    'DLA.Line',
                    'DLA.AlertID',
                    'DLA.OptTemp',
                    'DLA.OptPressure',
                    'DLA.TempAlertValue',
                    'DLA.AlertCNT',
                    'DLA.min_value',
                    'DLA.max_value',
                    'DLA.PressAlertValue',
                    'DLA.DevicesID',
                    'BB.Brand',
                    'Alt.AlertDescription',
                    'UD.FirstName',
                    'UD.LastName'
                )
                ->join('DeviceLines as DL', function ($join) {
                    $join->on('DLA.DevicesID', '=', 'DL.DevicesID')
                        ->on('DLA.line', '=', 'DL.line');
                })
                ->join('BeerBrands as BB', 'DL.BeerBrandsID', '=', 'BB.BeerBrandsID')
                ->join('Alerts as Alt', 'DLA.AlertID', '=', 'Alt.AlertID')
                ->leftJoin('UserDemographic as UD', 'DLA.UserAccountID', '=', 'UD.UserID')
                ->join('Devices as D', 'DLA.DevicesID', '=', 'D.DevicesID')
                ->where('D.LocationID', $locationId)
                ->where('DLA.RecordStatus', '=', '1')
                ->where('DLA.AckDateTime', '!=', '0000-00-00 00:00:00')
                ->whereBetween(
                    'DLA.AlertDateTime',
                    [$fromdate, $todate]
                )
                ->where('DLA.AlertID', '=', $request->alertType)->distinct()
                ->paginate($perPage);
        } else {
            $alerts = DB::table('DeviceLinesAlert as DLA')
                ->select(
                    DB::raw('DATE(DLA.AlertDateTime) as AckDate'),
                    'DLA.Line',
                    'DLA.DevicesID',
                    DB::raw('SUM(DLA.AlertCNT) as TotalAlertCNT'),
                    'BB.Brand',
                    'Alt.AlertDescription',
                    'UD.FirstName',
                    'UD.LastName',
                    'DLA.AlertID',
                    DB::raw("DATE_FORMAT(MAX(DLA.AlertDateTime), '%m-%d-%y %h:%i %p') as AlertDateTime"),
                    DB::raw("DATE_FORMAT(MAX(DLA.AckDateTime), '%m-%d-%y %h:%i %p') as AckDateTime"),
                )
                ->join('DeviceLines as DL', function ($join) {
                    $join->on('DLA.DevicesID', '=', 'DL.DevicesID')
                        ->on('DLA.line', '=', 'DL.line');
                })
                ->join('BeerBrands as BB', 'DL.BeerBrandsID', '=', 'BB.BeerBrandsID')
                ->join('Alerts as Alt', 'DLA.AlertID', '=', 'Alt.AlertID')
                ->leftJoin('UserDemographic as UD', 'DLA.UserAccountID', '=', 'UD.UserID')
                ->join('Devices as D', 'DLA.DevicesID', '=', 'D.DevicesID')
                ->where('D.LocationID', $locationId)
                ->where('DLA.RecordStatus', '=', '1')
                ->where('DLA.AckDateTime', '!=', '0000-00-00 00:00:00')
                ->whereBetween('DLA.AlertDateTime', [$fromdate, $todate])
                ->where('DLA.AlertID', '=', $request->alertType)
                ->groupBy('AckDate', 'DLA.Line', 'DLA.DevicesID', 'BB.Brand', 'Alt.AlertDescription', 'UD.FirstName', 'UD.LastName', 'DLA.AlertID')
                ->paginate($perPage);
        }

        foreach ($alerts as $alertList) {
            $alertList->describe = $this->getAlertDescription($alertList);
        }
        return $alerts;
    }

    // Location GloablQuery

    public function ChangeLocationLine(Request $request)
    {
        return $request->all();
    }

    public function LineManagementDevice($brand, $deviceid)
    {
        return DeviceLine::join('KegTypes', 'DeviceLines.KegTypeID', '=', 'KegTypes.KegTypeID')
            ->join('Devices', 'DeviceLines.DevicesID', '=', 'Devices.DevicesID')
            ->join('BeerBrands', 'DeviceLines.BeerBrandsID', '=', 'BeerBrands.BeerBrandsID')
            ->join('Distributors', 'DeviceLines.DistAccountID', '=', 'Distributors.DistributorID')
            // ->where('Devices.AccountID', $brand)
            ->where('DeviceLines.DevicesID', $deviceid)
            ->orderBy('Line')
            ->get();
    }

    public function showLogs()
    {
        $logPath = storage_path('logs/custom_log.log');

        if (file_exists($logPath)) {
            $logContent = file_get_contents($logPath);
        } else {
            $logContent = 'Log file not found.';
        }

        return view('log_viewer', ['logContent' => $logContent]);
    }


    /**
     * @OA\Get(
     *     path="/get-user-locations/{user_id}",
     *     summary="Get locations for given user",
     *     tags={"Location"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="User ID (Test User ID: 44)",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int32",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="An array of Locations",
     *         @OA\Schema(ref="#/components/schemas/Locations"),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getLocationsForUser($userId): Collection
    {
        return LocationService::LocationDisplay($userId);
    }
}
