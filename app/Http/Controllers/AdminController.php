<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public function index()
    {
        return view('Admins.index');
    }

    // return listing of accounts
    public function accounts()
    {
        $accounts = DB::table('Accounts')
            ->join('Location', 'Location.LocationID', '=', 'Accounts.LocationID')
            ->select('Accounts.*', 'Location.City', 'Location.EmailTechnical', 'Location.State')
            ->get();
        return view('Accounts.index', compact('accounts'));
    }

    // return listing of accounts
    public function addAccount()
    {
        $locations = DB::table('Location')
            ->where('LocationType', '!=', 2)
            ->get();
        return view('Accounts.add', compact('locations'));
    }

    // return listing of accounts
    public function saveAccount(Request $request)
    {
        $name = Session::get('userID');
        $EmailTechnical = $request->input('EmailTechnical');
        $city = $request->input('city');
        $userID = $request->input('userID');
        $accountName = $request->input('accountName');
        $address1 = $request->input('address1');
        $address2 = $request->input('address2');
        $email = $request->input('email');
        $record_status = $request->input('record_status', 'off');
        $phoneNumber = $request->input('phoneNumber');
        $state = $request->input('state');
        $zip = $request->input('zip');
        $LocationName = 'Primary Business';

        $location = new Location();
        $location->City = $city;
        $location->EmailTechnical = $EmailTechnical;
        $location->LocationType = 0;
        $location->LocationName = $LocationName;
        $location->State = $state;
        $location->PostalCode = $zip;
        $location->Email = $email;
        $location->Address1 = $address1;
        $location->Address2 = $address2;
        $location->PhonePrimary = $phoneNumber;
        // $location->UserID = $userID;      // now we will put account id in user id
        $location->save();
        $LocationID = $location->LocationID;
        if ($record_status == "on") {
            $status = 1;
        } else {
            $status = 0;
        }
        $newAccount = DB::table('Accounts')->insertGetId([
            'LocationID' => $LocationID,
            'ConfigurationID' => 0,
            'AccountName' => $accountName,
            'AccountLocationID' => $LocationID,
            'IsActive' => $status,
            'RecordStatus' => $status,
        ], 'AccountID');

        $location = Location::find($LocationID);
        $location->UserID = $newAccount;
        $location->save();
        Log::channel('custom_log')->info('Insertion:- Account: \'' . $accountName . '\' added by \'' . $name->UserID . '\' at ' . Carbon::now());
        return redirect('/accounts')->with('success', 'Account Added Successfully.');
        // return redirect('/accounts')->with('success', 'Account Added Successfully.');
    }

    // return listing of accounts
    public function editAccount($id)
    {
        $account = DB::table('Accounts')
            ->join('Location', 'Accounts.LocationID', '=', 'Location.LocationID')
            ->select('Accounts.*', 'Location.City', 'Location.State', 'Location.PostalCode', 'Location.Email', 'Location.Address1', 'Location.Address2', 'Location.EmailTechnical', 'Location.PhonePrimary')
            ->where('Accounts.AccountID', $id)
            ->first();
        $locations = DB::table('Location')
            ->where('LocationType', '!=', 2)
            ->get();
        return view('Accounts.edit', compact('account', 'locations'));
    }

    // return listing of accounts
    public function updateAccount(Request $request)
    {

        $name = Session::get('userID');
        $locationID = $request->input('locationID');
        $city = $request->input('city');
        $userID = $request->input('userID');
        $accountName = $request->input('accountName');
        $address1 = $request->input('address1');
        $address2 = $request->input('address2');
        $email = $request->input('email');
        $record_status = $request->input('record_status', 'off');
        $phoneNumber = $request->input('phoneNumber');
        $state = $request->input('state');
        $zip = $request->input('zip');
        $EmailTechnical = $request->input('EmailTechnical');
        $AccountID = $request->input('AccountID');

        $location = Location::where('LocationID', $locationID)->first();

        if ($location) {
            // Update the location record with the new values
            $location->City = $city;
            $location->State = $state;
            $location->PostalCode = $zip;
            $location->Email = $email;
            $location->Address1 = $address1;
            $location->Address2 = $address2;
            $location->EmailTechnical = $EmailTechnical;
            $location->PhonePrimary = $phoneNumber;
            $location->UserID = $AccountID;
            $location->save();
        }

        if ($record_status == "on") {
            $status = 1;
        } else {
            $status = 0;
        }

        // Update the account record in the Accounts table
        DB::table('Accounts')
            ->where('AccountID', $AccountID)
            ->update([
                'AccountName' => $accountName,
                'IsActive' => $status,
                'RecordStatus' => $status,
            ]);

        Log::channel('custom_log')->info('Updation:- Account: \'' . $accountName . '\' updated by \'' . $name->UserID . '\' at ' . Carbon::now());
        return redirect('/accounts')->with('success', 'Account Updated Successfully.');
        // return redirect()->back()->with('success', 'Account Updated Successfully.');

    }

    public function locationData($id): array
    {
        $location = DB::table('Location')
            ->where('LocationID', $id)
            ->first();
        return ['location' => $location];
    }

    public function deleteAccount($id): array
    {
        $device = DB::table('Devices')
            ->where('AccountID', $id)
            ->first();

        if ($device) {
            $deviceName = $device->Name;
            return ['status' => 401, 'message' => "This account is linked with a device: $deviceName"];
        }

        DB::table('Accounts')
            ->where('AccountID', $id)
            ->delete();

        return ['status' => 'success', 'message' => "Account deleted successfully."];
    }
}
