<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;

class ApiUserController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function loginDashboard(Request $request)
    {
        $result = $this->authService->login($request, true); // API Login (true)

        if (isset($result['error']) && $result['error']) {
            return response()->json(['error' => $result['message']], $result['statusCode']);
        }

        return response()->json($result['data'], 200);
    }
}
