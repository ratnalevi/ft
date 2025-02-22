<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LocationService
{
    public static function LocationDisplay($userID = null): Collection
    {
        if (is_null($userID)) {
            $name = Session::get('userID');
            $userID = $name->UserID;
        }

        $configurationID = 9999;
        return DB::table('Location AS l')
            ->select('l.UserID AS AccountID', 'l.LocationID', 'l.LocationName')
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
            ->orderBy('l.LocationName')
            ->get();
    }
}
