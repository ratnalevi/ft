<?php
namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use App\Models\UserSession;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\UserAccount;


class AuthService
{
    public function login($request, $isApi = false)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required',
            ]
        );

        if ($validator->fails()) {
            return ['error' => true, 'message' => $validator->errors(), 'statusCode' => '400', 'validationError' => true];
        }

        $userActive = DB::table('User')
            ->join('UserAccount', 'User.UserID', '=', 'UserAccount.UserID')
            ->select('*', 'UserAccount.AdminAccess', 'User.UserID as UserID')
            ->where('User.email', $request->email)
            ->where('UserAccount.IsActive', '0')
            ->first();
        if ($userActive) {
            return ['error' => true, 'message' => 'User inactive', 'statusCode' => '403'];
        }

        // Check if email and password match a user in the database
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->Password)) {
            // Invalid credentials
            return ['error' => true, 'message' => 'Invalid email or password', 'statusCode' => '401'];
        }

        // Get user role
        $userRole = UserAccount::where('UserID', $user->UserID)->value('AdminAccess');

        if ($isApi) {
            // API Login: Generate Token
            $token = $user->createToken('AuthToken')->plainTextToken;

            return [
                'error' => false,
                'message' => 'Login Successful',
                'data' => [
                    'user' => [
                        'UserID' => $user->UserID,
                        'email' => $user->email,
                        'admin_access' => $userRole,
                    ],
                    'token' => $token,
                ],
            ];
        } else {
            // Web Login: Store session data
            session(['admin_access' => $userRole]);
            Session::put('user', $user);
            Session::put('user_id', $user->UserID);

            Cache::put('myData', 'Hello, world!', 60);
            session(['userID' => $user]);

            $is_admin = 1;
            $this->SessionCreate($user, $is_admin);

            return [
                'message' => 'Login Successful',
            ];
        }
    }

    private function SessionCreate($user, $is_admin)
    {
        if ($is_admin) {
            $userSession = new UserSession();
            $existSession = UserSession::where('UserID', $user->UserID)
                ->orderBy('UserID', 'DESC')
                ->first();
        } else {
            $userSession = new UserSession();
            $existSession = UserSession::where('UserID', $user->UserID)
                ->orderBy('UserID', 'DESC')
                ->first();
        }

        $userSession->UserID = $user->UserID;
        $userSession->SessionDateTime = date('Y-m-d H:i:s');
        $userSession->ExpireSeconds = 120;

        if ($existSession) {
            $userSession->SessionID = $existSession->SessionID + 1;
        } else {
            $userSession->SessionID = '1';
        }

        $userSession->RecordStatus = "1";
        $userSession->InsertDateTime = date('Y-m-d H:i:s');
        $userSession->UpdateDateTime = date('Y-m-d H:i:s');
        $userSession->save();
    }
}
