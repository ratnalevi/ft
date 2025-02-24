<?php

use App\Http\Controllers\AzureController;
use App\Http\Controllers\InRangeController;
use App\Http\Controllers\LineDataController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Api\ApiUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Grouping existing APIs under v1
Route::prefix('v1')->group(function () {

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return response()->json(['user' => $request->user()], 200);
        });
        Route::post('/logout', [ApiUserController::class, 'logoutUser']);
    });

    Route::post('/login', [ApiUserController::class, 'loginDashboard']);
    Route::post('/refresh', [ApiUserController::class, 'refreshToken']);

    Route::get('/get/brand/{location_id}', [LineDataController::class, 'BrandGetAll']);
    Route::get('/load/devices/{location_id}', [UserController::class, 'loadDevices']);
    Route::get('/getHomeAlerts/{location_id}', [UserController::class, 'getHomeAlerts']);
    Route::get('/load/line/data/{device_id}/{from_date}/{to_date}/{page_number}', [UserController::class, 'LoadLineDate']);
    Route::get('/in-range-report-data/{device_id}/{from}/{to}', [InRangeController::class, 'inRangeReportData']);
    Route::get('/pour-score-detail-data/{device}/{from}/{to}', [LineDataController::class, 'pourScoreDetail']);
    Route::get('/get-user-locations/{user_id}', [UserController::class, 'getLocationsForUser']);

    Route::get('/load/line/data/{brand}/{type}/{daysfilter}/{devices}', [LineDataController::class, 'reportApi']);
    Route::get('/load/line-sensor/data/{brand}/{type}/{daysfilter}/{daysfilter2}', [LineDataController::class, 'reportApiSensor']);
    Route::get('/AlertCenter', [AzureController::class, 'AlertCenter1']);

    Route::middleware(['auth:api'])->group(function () {

        // Pour score detail report
        Route::get('/pour-score-detail', [UserController::class, 'pourScoreDetailReport'])->name('pour-score-detail');

        // In Range report
        Route::get('/in-range-reports', [InRangeController::class, 'InRangeReport'])->name('in-range-report');
    });
});