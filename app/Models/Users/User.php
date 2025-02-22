<?php

namespace App\Models\Users;

use App\Http\Traits\CheckServer;
use App\Models\Location;
use App\Models\UserAccount;
use App\Models\UserDemoGraphic;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class User extends Model
{
    use HasFactory;
    use CheckServer;

    protected $table = 'User';
    protected $primaryKey = 'UserID';
    protected $fillable = ['LoginID', 'Salt', 'Password', 'LastLoginDateTime', 'AllowLogin', 'RecordStatus', 'InsertDateTime', 'UpdateDateTime'];

    public static function CheckHostingUser()
    {
        if (env('DB_HOST') == '127.0.0.1') {
            return 'local';
        } else {
            return 'server';
        }
    }

    public static function AddUsers($request)
    {
        return $request->record_status ? 1 : 0;
    }

    // Validation check for adding new user
    public static function Validation($request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required',
                'last_name' => 'required',
                'location' => 'required',
                'location1' => 'required',
                'email' => 'required|unique:User,email',
                'password' => 'required',
            ]
        );

        if ($validator->fails()) {
            return $validator->errors();
        }
    }

    // Add New User
    public static function AddUser($request)
    {
        if (strpos($request->location1, $request->location) !== false) {
            $name = Session::get('userID');
            if ($request->record_status == 'true') {
                $record_status = 1;
            } else {
                $record_status = 0;
            }
            if ($request->allow_login == 'true') {
                $access = 1;
            } else {
                $access = 0;
            }


            $id = DB::table('User')->insertGetId(
                [
                    'Email' => $request->email,
                    'Password' => Hash::make($request->password),
                    'InsertDateTime' => date('Y-m-d H:i:s')
                ]
            );

            if ($id) {
                $userId = User::where('UserID', $id)->pluck('UserID')->first();

                $locationS = '111';
                $locationString = explode(',', $request->location1);
                $userIDs = Location::whereIn('LocationID', $locationString)
                    ->pluck('UserID');

                if (in_array($locationS, $locationString) || $request->location1 == $locationS) {

                    $user_account = array(
                        'UserID' => $userId,
                        'AccountID' => '42',
                        'LocationID' => '111',
                        'ConfigurationID' => '9999',
                        'AdminAccess' => $access,
                        'IsActive' => $record_status,
                        'RecordStatus' => '1',
                        'InsertDateTime' => date('Y-m-d H:i:s')
                    );
                    UserAccount::create($user_account);
                } else {

                    // $locationString = explode(",", $request->location1);
                    if (isset($request->location1)) {
                        foreach ($locationString as $item) {

                            // need to review this again
                            //  $userID = Account::select('AccountID')->where('LocationID', $item)->first();
                            // $userID = DB::table('Location')->where('LocationID', $item)->value('UserID');

                            $result = Location::select('UserID as AccountID', 'LocationID', 'LocationName')
                                ->where('LocationType', 1)
                                ->where('LocationID', $item)
                                ->orderBy('LocationName')
                                ->get();
                            $accountID = $user_account = array(
                                'UserID' => $userId,
                                'AccountID' => $result[0]->AccountID,
                                'LocationID' => $item,
                                'ConfigurationID' => '0',
                                'AdminAccess' => $access,
                                'IsActive' => $record_status,
                                'RecordStatus' => '1',
                                'InsertDateTime' => date('Y-m-d H:i:s')
                            );
                            UserAccount::create($user_account);
                        }
                    }
                }

                $user_demographic = array(
                    'UserID' => $userId,
                    'FirstName' => $request->first_name,
                    'LastName' => $request->last_name,
                    'LocationID' => $request->location,
                    'AllowLogin' => $record_status,
                    'RecordStatus' => '1',
                    'InsertDateTime' => date('Y-m-d H:i:s')
                );
                UserDemoGraphic::create($user_demographic);
                $locationAdd = array(
                    'LocationType' => '2',
                    'UserType' => '0',
                    'UserID' => $userId,
                    'PhonePrimary' => $request->phone,
                    'InsertDateTime' => date('Y-m-d H:i:s')
                );
                Location::create($locationAdd);
                Log::channel('custom_log')->info('Insertion:- User: \'' . $request->first_name . ' ' . $request->last_name . '\' added by \'' . $name->UserID . '\' at ' . Carbon::now());
                return response()->json(['status' => 'add']);
            }
        } else {
            return response()->json(['status' => 'notexists']);
        }
    }
}
