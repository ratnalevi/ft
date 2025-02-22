<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AzureController;
use App\Http\Controllers\DeviceManagementController;
use App\Http\Controllers\InRangeController;
use App\Http\Controllers\LineDataController;
use App\Http\Controllers\PosItemController;
use App\Http\Controllers\SwaggerController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Register
// Route::get('/register', [UserController::class, 'viewregister']);
// Route::post('/register', [UserController::class, 'register']);

// Swagger Index
Route::get('/swagger', [SwaggerController::class, 'index']);

// Dashboard
Route::post('/EventIOThub', [AzureController::class, 'EventIOThub']);
Route::post('/EventIOThubAuth', [AzureController::class, 'EventIOThubAuth']);
Route::post('/AlertCenter', [AzureController::class, 'AlertCenter']);
Route::get('/filters', [AzureController::class, 'filters']);

// Azure connection
Route::get('/azureConnection', [AzureController::class, 'azureConnection']);
Route::get('/azureform', [AzureController::class, 'azureform']);
Route::post('/azureAddDevice', [AzureController::class, 'addDevice']);

Route::post('/testing.php', [AzureController::class, 'processLineData']);

// Change Locations
Route::get('/change_location', [UserController::class, 'ChangeLocation'])->name('location.get');

// ACK Data
Route::get('/ack-data', [UserController::class, 'AckData'])->name('ack.post');

// Change Locations
Route::get('/alert-center-data', [UserController::class, 'AlertData'])->name('AlertData.get');

// Line Management
Route::get('/Line-management/{brand}/{deviceid}', [UserController::class, 'LineManagementDevice'])->name('LineManagementDevice.get');

//ub aid code for trend analysis
Route::get('/trend/analysis/load/data/{brand}/{fromdate_span1}/{todate_span1}/{fromdate_span2}/{todate_span2}/{type}', [LineDataController::class, 'LoadAnalysis']);
Route::get('/test11', [LineDataController::class, 'test']);

// Login
Route::get('/', [UserController::class, 'viewlogin']);
Route::get('/login', [UserController::class, 'login']);
Route::post('/login', [UserController::class, 'loginDashboard']);
// Logout
Route::get('/logout', [UserController::class, 'logout']);

Route::middleware('admin')->group(function () {
    // Routes that require admin authentication
    Route::get('/device-management', [DeviceManagementController::class, 'Index']);
    Route::get('/accounts', [AdminController::class, 'accounts']);
    Route::get('/add-account', [AdminController::class, 'addAccount']);
    Route::post('/save/account', [AdminController::class, 'saveAccount']);
    Route::get('/edit-account/{id}', [AdminController::class, 'editAccount']);
    Route::get('/delete-account/{id}', [AdminController::class, 'deleteAccount']);
    Route::post('/update-account', [AdminController::class, 'updateAccount']);
    Route::post('/get/locationData/{id}', [AdminController::class, 'locationData']);
});

Route::middleware(['auth'])->group(function () {

    Route::get('/PosItem', [PosItemController::class, 'index']);
    Route::post('/PosItem', [PosItemController::class, 'create']);
    Route::get('/PosItem/{id}/edit', [PosItemController::class, 'edit']);
    Route::patch('/PosItem/{id}/update', [PosItemController::class, 'update']);
    Route::delete('/PosItem', [PosItemController::class, 'destroy']);

    Route::get('/get-pos-items', [PosItemController::class, 'getPosItems']);

    Route::get('/load/line/data/{device_id}/{from_date}/{to_date}/{page_number}', [UserController::class, 'LoadLineDate']);
    Route::get('/load/line/summery/date/{location}/{pagenumber}', [LineDataController::class, 'LoadLineSummary']);

    Route::get('/home', [UserController::class, 'home'])->name('ajax-pagi');
    Route::get('/update-alert-expiry', [UserController::class, 'updateAlertExpiry'])->name('update-alert-expiry');
    Route::get('/getHomeAlerts/{location_id}', [UserController::class, 'getHomeAlerts']);
    Route::get('/line/summary', [LineDataController::class, 'lineSummary'])->name('ajax-pagi2');
    Route::get('/load/devices/{location_id}', [UserController::class, 'loadDevices']);

    // User Admin
    Route::get('/floteq-user-admin', [UserController::class, 'FloteqUserAdmin']);

    // User Management
    Route::get('/line-management', [UserController::class, 'LineManagement']);
    Route::get('/location-management', [UserController::class, 'LocationManagement']);
    Route::get('/brand-management', [UserController::class, 'BrandManagement']);
    Route::get('/alert-management', [UserController::class, 'AlertManagement']);

    // Alert center
    Route::get('/alert-center', [UserController::class, 'AlertCenter']);

    // Documentation
    Route::get('/documentation', [UserController::class, 'Documentation']);
    Route::get('/documentationUsage', [UserController::class, 'documentationUsage']);
    Route::get('/documentationPolicy', [UserController::class, 'documentationPolicy']);
    Route::get('/documentationSaas', [UserController::class, 'documentationSaas']);

    // User Reporting
    Route::get('/user-reporting', [UserController::class, 'UserReporting']);

    // Line Sensor
    Route::get('/line-reporting', [UserController::class, 'LineReporting']);
    Route::get('/line-reportingPres', [UserController::class, 'LineReporting2']);
    Route::get('/line-reportingTDS', [UserController::class, 'LineReporting3']);

    Route::get('/sensor-reporting', [UserController::class, 'SensorReporting']);
    Route::get('/brand-comparison', [UserController::class, 'BrandComparison']);
    Route::get('/pourscore-reports', [UserController::class, 'PourscoreReport']);
    Route::get('/trend-analysis', [UserController::class, 'TrendAnalysis']);

    // User managements
    Route::get('/add-user', [UserController::class, 'addUser']);
    Route::post('/save/user', [UserController::class, 'saveUser']);
    Route::get('/edit/account/{id}', [UserController::class, 'AccountEdit']);
    Route::get('/delete/account/{id}', [UserController::class, 'AccountDelete']);
    Route::post('/update/user', [UserController::class, 'updateUser']);
    Route::get('/logs', [UserController::class, 'showLogs']);

    // Line Management
    Route::get('/add-linemanagement/{selectedLocation}/{selectedDevice}', [UserController::class, 'addlinemanagement']);
    Route::get('/check/line', [UserController::class, 'CheckLine']);
    Route::post('/save/device', [LineDataController::class, 'saveDevice'])->name('/save/device');
    Route::get('/delete/line/{id}', [LineDataController::class, 'LineDelete']);
    Route::get('/edit-linemanagement/{devicelinesid}', [UserController::class, 'editlinemanagement']);
    Route::post('/update/device', [LineDataController::class, 'updateDevice'])->name('/update/device');

    // location Management
    Route::get('/add-location', [UserController::class, 'addLocation']);
    Route::post('/save/location', [LineDataController::class, 'saveLocation'])->name('savelocation');
    Route::get('/connection', [AzureController::class, 'connection']);
    Route::get('/connectiondata', [AzureController::class, 'connectiondata']);

    // device-management
    Route::get('/add-Devimanagement', [DeviceManagementController::class, 'addDevice']);
    Route::get('/editDevimanagement/{id}', [DeviceManagementController::class, 'editDevice']);
    Route::get('/deleteDevimanagement/{id}', [DeviceManagementController::class, 'deletedDevice']);
    Route::get('/device/change/{id}', [DeviceManagementController::class, 'changeStatus']);
    Route::get('/getLocation/Devimanagement/{id}', [DeviceManagementController::class, 'getLocation']);

    // Update save func
    Route::post('/update-Devimanagement', [DeviceManagementController::class, 'updateDevice']);
    Route::post('/save-Devimanagement', [DeviceManagementController::class, 'saveDevice']);
    Route::post('/check-serialt', [DeviceManagementController::class, 'checkSerial'])->name('checkSerial');

    // Update
    Route::get('/edit/location/{locationid}', [UserController::class, 'editLocation']);
    Route::post('/update/location', [LineDataController::class, 'updateLocation'])->name('updateLocation');
    // Delete
    Route::get('/delete/location/{locationid}', [UserController::class, 'deleteLocation']);

    // brand management
    Route::get('/add-brand', [UserController::class, 'addBrand']);
    Route::get('/editbrand/{id}', [UserController::class, 'editBrand']);
    Route::post('/save/brand', [LineDataController::class, 'saveBrand']);

    // alert management
    Route::get('/add-alert', [UserController::class, 'addAlert']);
    Route::get('/editalert/{id}', [UserController::class, 'editAlert']);
    Route::post('/save/alert', [LineDataController::class, 'saveAlert']);

    // Reports URL
    Route::get('/sensor-reporting-data/{beerbrandID}/{temperature}/{daysfilter}/{deviceid}', [LineDataController::class, 'SensorReporting']);
    Route::get('/brand-comparison/load/data/{location?}/{devices?}/{types?}/{from?}/{to?}', [LineDataController::class, 'brandComparison']);
    Route::get('/pourscore-report/{location}/{types}', [LineDataController::class, 'pourscoreReport']);
    Route::get('/trend-analysis/load/data/{location}', [LineDataController::class, 'TrendAnalysis']);
    Route::get('/get/devices/against/brand/{brand}', [LineDataController::class, 'GetDevicesAgainstBrand']);
    Route::get('/get/devices/{id}', [LineDataController::class, 'LocationGetAll']);
    Route::get('/get/brand/{location_id}', [LineDataController::class, 'BrandGetAll']);
    Route::get('/get/devices/ids/{brand}', [LineDataController::class, 'getDevicesIds']);
    Route::get('/get/devices/lines/{id}', [LineDataController::class, 'getDevicesLine']);

    // Pour score detail report
    Route::get('/pour-score-detail', [UserController::class, 'pourScoreDetailReport'])->name('pour-score-detail');
    Route::get('/pour-score-detail-data/{device}/{from}/{to}', [LineDataController::class, 'pourScoreDetail']);

    // In Range report
    Route::get('/in-range-reports', [InRangeController::class, 'InRangeReport'])->name('in-range-report');
    Route::get('/in-range-report-data/{device_id}/{from}/{to}', [InRangeController::class, 'inRangeReportData']);
});

//Auth::routes();

