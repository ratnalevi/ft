<?php

namespace App\Http\Controllers;

use App\Models\Devices;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceManagementController extends Controller
{
    public function Index()
    {
        return Devices::DeviceIndex();
    }

    public function addDevice(Request $request)
    {
        return Devices::DeviceAdd();
    }

    public function editDevice(Request $request, $id)
    {
        return Devices::DeviceEdit($id);
    }

    public function deletedDevice(Request $request, $id)
    {
        return Devices::DeviceDelete($id);
    }

    public function updateDevice(Request $request)
    {
        return Devices::DeviceUpdate($request);
    }

    public function saveDevice(Request $request)
    {
        return Devices::DeviceCreate($request);
    }

    public function changeStatus(Request $request, $id)
    {
        Devices::where('DevicesID', $id)->update([
            'RecordStatus' => $request->RecordStatus,
            'UpdateDateTime' => date('Y-m-d H:i:A'),
        ]);

        return ['status' => 'success', 'message' => 'Status Updated Successfully.'];
    }

    public function getLocation($id)
    {
        $locations = Location::select('UserID AS AccountID', 'LocationID', 'LocationName')
            ->where('LocationType', 1)
            ->where('UserID', $id)
            ->orderBy('LocationName')
            ->get();

        return response()->json($locations);
    }

    public function checkSerial(Request $request)
    {
        $serial = $request->input('serial');

        $device = DB::table('Devices')
            ->where('DevicesID', $serial)
            ->orWhere('Serial', $serial)
            ->first();

        if ($device) {
            // Serial is already in use
            return response()->json(['status' => 'error', 'message' => 'Serial is already in use']);
        }

        // Serial is unique
        return response()->json(['status' => 'success', 'message' => 'Serial is unique']);
    }
}
