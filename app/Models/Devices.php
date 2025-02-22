<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class Devices extends Model
{
    use HasFactory;

    public const CREATED_AT = 'InsertDateTime';
    public const UPDATED_AT = 'UpdateDateTime';
    protected $table = "Devices";
    protected $primaryKey = 'DevicesID';
    protected $fillable =
        [
            "DevicesID",
            "AccountID",
            "LocationID",
            "Name",
            "Serial",
            "SSID",
            "WIFIPWD",
            "AzureHP",
            "RptInt1_Time",
            "RptInt1_IntSec",
            "RptInt2_Time",
            "RptInt2_IntSec",
            "RptInt3_Time",
            "RptInt3_IntSec",
            "RecordStatus",
            "InsertDateTime",
            "UpdateDateTime",
        ];

    public static function DeviceCreate($request)
    {
        $name = Session::get('userID');
        DB::beginTransaction();
        $validatedData = $request->validate([
            'accountName' => 'required',
            'locationName' => 'nullable',
            'deviceName' => 'required',
            'wifiSSID' => 'nullable',
            'wifiPASS' => 'nullable',
            'deviceSerial' => 'required|unique:Devices,Serial',
        ]);
        try {
            $azurehost = $request->input('azurehost') ?? '';

            $device = new Devices();
            $device->AccountID = $request->accountName;
            if (!empty($request->locationName)) {
                $device->LocationID = $request->locationName;
            } else {
                // Handle the case when location name is empty
                $device->LocationID = '0';
            }
            $device->DevicesID = $request->deviceSerial;
            $device->Name = $request->deviceName;
            $device->Serial = $request->deviceSerial;
            $device->SSID = $request->wifiSSID;
            $device->WIFIPWD = $request->wifiPASS;
            $device->AzureHP = $request->azurehost;
            $device->RptInt1_Time = $request->time;
            $device->RptInt1_IntSec = $request->seconds;
            $device->RptInt2_Time = $request->time1;
            $device->RptInt2_IntSec = $request->seconds1;
            $device->RptInt3_Time = $request->time2;
            $device->RptInt3_IntSec = $request->seconds2;
            $device->RecordStatus = 1;
            $device->InsertDateTime = date('Y-m-d H:i:s');
            $device->UpdateDateTime = date('Y-m-d H:i:s');
            $device->save();

            $deviceId = $device->DevicesID;

            // if ($request->has('iotcheck'))
            //     $iotEdge = true;
            // else
            //     $iotEdge = false;
            // $hubName = 'DantesFirstIoTHub';
            // $sharedAccessKey = 'kfYQbV+J8/O6Gmbb1JixjTK0JOT0PuzCnDcdIOk0HYI=';
            // $sharedAccessKeyName = 'iothubowner';
            // $apiVersion = '2018-06-30'; // Example API version, please verify the correct version for your Azure IoT Hub

            // $sasToken = AzureController::generateSasToken($hubName, $sharedAccessKeyName, $sharedAccessKey);
            // $url = "https://{$hubName}.azure-devices.net/devices/{$deviceId}?api-version={$apiVersion}";

            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, $url);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            // curl_setopt($ch, CURLOPT_HTTPHEADER, [
            //     'Authorization: ' . $sasToken,
            //     'Content-Type: application/json'
            // ]);
            // $requestBody = [
            //     'deviceId' => $deviceId,
            //     'capabilities' => [
            //         'iotEdge' => $iotEdge
            //     ]
            // ];
            // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
            // $response = curl_exec($ch);
            // curl_close($ch);

            // if (strpos($response, 'ErrorCode') !== false) {
            //     DB::rollback();
            //     return redirect()->back()->with('error', $response);
            // }

            /////lines insertion


            /*
            $totalLines = 24; // Total number of lines to insert

            for ($line = 1; $line <= $totalLines; $line++) {
                DB::table('DeviceLines')->insert([
                    'DevicesID' => $deviceId,
                    'DeviceStatus' => '0',
                    'Line' => $line,
                    'BeerTubingID' => '2',
                    'BeerBrandsID' => '153',
                    'KegTypeID' => '3',
                    'DistAccountID' => '3',
                    'OZFactor' => '1.15',
                    'OptTemp' => '0.00',
                    'OptPressure' => '0.00',
                    'TempPressAlert' => '0',
                    'TempPressAlertTimeOut' => '0',
            'TempAlertValue' => '0.00',
            'PressAlertValue' => '0.00',
            'Pressure' => '0.00',
            'KegCost' => '0',
            'LineLength' => '0',
            'LineType' => '0',
            'LastPour' => '0',
            'LastPourDateTime' => '0000-00-00 00:00:00',
            'RecordStatus' => '0',
            'InsertDateTime' => '0000-00-00 00:00:00',
            'UpdateDateTime' => '0000-00-00 00:00:00',
            'updated_at' => now(),
            'created_at' => now(),
        ]);
    }
    */

            //////


            DB::commit();
            Log::channel('custom_log')->info('Insertion:- Device: \'' . $request->deviceName . '\' added by \'' . $name->UserID . '\' at ' . Carbon::now());
            return redirect('/device-management')->with('success', 'Device Added Successfully.');
            // return redirect()->back()->with('success', 'Device Added Successfully.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect('/device-management')->with('error', 'An error occurred while creating the device.' . $e->getMessage());
            // return redirect()->back()->with('error', 'An error occurred while creating the device.' . $e->getMessage());
        }
    }

    public static function DeviceUpdate($request)
    {

        $name = Session::get('userID');
        $validatedData = $request->validate([
            'deviceSerial' => 'required',
            'accountName' => 'required',
            'locationName' => 'nullable',
            'deviceName' => 'required',
            'azurehost' => 'nullable',
            'wifiPASS' => 'nullable',
            // 'deviceSerial' => [
            //     'required',
            //     Rule::unique('Devices', 'Serial')->ignore($request->id, 'DevicesID'),
            // ],
        ]);

        $locationNew = "0";
        if (!empty($request->locationName)) {
            $locationNew = $request->locationName;
        }
        $deviceSerial = $request->deviceSerial;

        Devices::where('DevicesID', $request->id)->update([
            'AccountID' => $request->accountName,
            'LocationID' => $locationNew,
            'Name' => $request->deviceName,
            'DevicesID' => $request->deviceSerial,
            'Serial' => $request->deviceSerial,
            'SSID' => $request->wifiSSID,
            'WIFIPWD' => $request->wifiPASS,
            'AzureHP' => $request->azurehost,
            'RptInt1_Time' => $request->time,
            'RptInt1_IntSec' => $request->seconds,
            'RptInt2_Time' => $request->time1,
            'RptInt2_IntSec' => $request->seconds1,
            'RptInt3_Time' => $request->time2,
            'RptInt3_IntSec' => $request->seconds2,
            'RecordStatus' => 1,
            'InsertDateTime' => date('Y-m-d H:i:A'),
            'UpdateDateTime' => date('Y-m-d H:i:A'),
        ]);
        Log::channel('custom_log')->info('Updation:- Device: \'' . $request->deviceName . '\' updated by \'' . $name->UserID . '\' at ' . Carbon::now());
        return redirect('/device-management')->with('success', 'Device Updated Successfully.');

        // return redirect('/editDevimanagement/' . urlencode($deviceSerial))->with('success', 'Device Updated Successfully');
        // return redirect()->back()->with('success', 'Device Updated Successfully');
    }

    public static function DeviceDelete($id)
    {
        Devices::where('DevicesID', $id)->delete();
        return redirect()->back()->with('success', 'Device Deleted Successfully');
    }

    public static function DeviceEdit($id)
    {
        $accounts = Account::orderBy('AccountName')->get();
        // $locations = Location::orderBy('LocationName')->get();
        $deviceID = Devices::where('DevicesID', $id)->first();
        // $locations = LocationService::LocationDisplay();
        $locations = Location::select('UserID AS AccountID', 'LocationID', 'LocationName')
            ->where('LocationType', 1)
            ->orderBy('LocationName')
            ->get();
        return view('devices.editNew', compact('accounts', 'locations', 'deviceID'));
    }

    public static function DeviceAdd()
    {
        $accounts = Account::orderBy('AccountName')->get();
        // $locations = Location::orderBy('LocationName')->get();
        // $locations = LocationService::LocationDisplay();
        $locations = Location::select('UserID AS AccountID', 'LocationID', 'LocationName')
            ->where('LocationType', 1)
            ->orderBy('LocationName')
            ->get();

        return view('devices.addNew', compact('accounts', 'locations'));
    }

    public static function DeviceIndex()
    {
        $device = Devices::leftJoin('Accounts', 'Devices.AccountID', '=', 'Accounts.AccountID')
            ->leftJoin('Location', 'Devices.LocationID', '=', 'Location.LocationID')
            ->select('Devices.*', 'Location.LocationName', 'Accounts.AccountName')
            ->get();

        return view('devices.index', compact('device'));
    }
}
