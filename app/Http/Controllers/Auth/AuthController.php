<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRequest;
use App\Services\AuthService\AuthService;

class AuthController extends Controller
{
    protected $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function authorizingUser(AuthRequest $request)
    {
        $response = $this->service->authorizingUser($request->validated());
        return response()->json($response, $response['code']);
    }

    public function authorizingGoogleAccess(AuthRequest $request)
    {
        $response = $this->service->authorizingGoogleAccess($request->validated());
        return response()->json($response, $response['code']);
    }

    public function googleAccessStatus()
    {
        $response = $this->service->googleAccessStatus();
        return response()->json($response, $response['code']);
    }

    public function logout()
    {
        $response = $this->service->revokeAccess();
        return response()->json($response, $response['code']);
    }
}