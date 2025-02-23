<?php
namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
            // Revoke old tokens
            $user->tokens()->delete();
            // Generate tokens
            $accessToken = $user->createToken('auth_token')->plainTextToken;
            $refreshToken = $user->createToken('refresh_token')->plainTextToken;

            return [
                'error' => false,
                'message' => 'Login Successful',
                'data' => [
                    'user' => [
                        'UserID' => $user->UserID,
                        'email' => $user->email,
                        'admin_access' => $userRole,
                    ],
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken,
                    'token_type' => 'Bearer',
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

    public function refresh($request, $isApi = false)
    {
        $request->validate([
            'refresh_token' => 'required'
        ]);

        // Find user by refresh token
        $user = User::whereHas('tokens', function ($query) use ($request) {
            $query->where('token', hash('sha256', $request->refresh_token));
        })->first();

        if (!$user) {
            return ['error' => true, 'message' => 'Invalid refresh token', 'statusCode' => '401'];
        }

        // Revoke all tokens
        $user->tokens()->delete();

        // Generate new tokens
        $accessToken = $user->createToken('auth_token')->plainTextToken;
        $refreshToken = $user->createToken('refresh_token')->plainTextToken;

        return [
            'error' => false,
            'message' => 'Token Refreshed',
            'data' => [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer',
            ],
        ];
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
