<?php
namespace App\Services\AuthService;

use App\Models\GoogleAuth;
use App\Utils\GoogleConfig;
use App\Models\User;
use App\Utils\LogGenerator;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    use GoogleConfig, LogGenerator;

    protected $google;
    protected $googleInternal;

    public function __construct()
    {
        $this->google = $this->googleLogin();
        $this->googleInternal = $this->googleInternal();
    }

    public function authorizingUser(array $data) : array
    {
        $result = ['code' => 200, 'message' => 'Berhasil login', 'data' => []];
        try {
            $token = $this->google->fetchAccessTokenWithAuthCode($data['code']);

            if (isset($token['access_token']) && isset($token['id_token']) && $payload = $this->google->verifyIdToken($token['id_token'])) {
                $credentials = [
                    'email' => $payload['email'],
                    'password' => getenv('APP_USER_DEFAULT_PASSWORD'),
                ];
                
                if (!$token = Auth::attempt($credentials)) {
                    $result['code'] = 401;
                    $result['message'] = 'Unauthorized';
                    return $result;
                }

                User::where('email', $payload['email'])->update(['foto' => $payload['picture']]);
                
                $result['data'] = [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'user' => [
                        'name' => auth()->user()->name,
                        'photo' => $payload['picture'],
                        'role' => auth()->user()->role,
                    ]
                ];
            } else {
                $result['code'] = 401;
                $result['message'] = 'Unauthorized';
            }
        } catch (Exception $e) {
            $this->storeLog('system', 'App\Services\AuthService\AuthService@authorizingUser', $e->getMessage());
            $result['code'] = 500;
            $result['message'] = 'Terjadi kesalahan server';
        }

        return $result;
    }

    public function authorizingGoogleAccess(array $data): array
    {
        $result = ['code' => 200, 'message' => 'Berhasil login', 'data' => []];
        try {
            $token = $this->googleInternal->fetchAccessTokenWithAuthCode($data['code']);

            if (isset($token['access_token']) && isset($token['id_token']) && $payload = $this->googleInternal->verifyIdToken($token['id_token'])) {
                GoogleAuth::whereNull('deleted_at')->delete();
                GoogleAuth::insert([
                    'name' => $payload['name'],
                    'email' => $payload['email'],
                    'credentials' => json_encode($token),
                    'refresh_token' => $token['refresh_token'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            } else {
                $result['code'] = 401;
                $result['message'] = 'Unauthorized';
            }
        } catch (Exception $e) {
            $this->storeLog('system', 'App\Services\AuthService\AuthService@authorizingGoogleAccess', $e->getMessage());
            $result['code'] = 500;
            $result['message'] = 'Terjadi kesalahan server';
        }

        return $result;
    }

    public function googleAccessStatus()
    {
        $result = ['code' => 200, 'message' => 'status login', 'data' => []];
        try {
            $token = GoogleAuth::first();
            $decodedToken = json_decode($token->credentials, true);
            $this->googleInternal->setAccessToken($decodedToken);
            if ($this->googleInternal->isAccessTokenExpired()) {
                $newToken = $this->googleInternal->refreshToken($token->refresh_token);
                $payload = $this->googleInternal->verifyIdToken($newToken['id_token']);
                GoogleAuth::where('id', $token->id)->update([
                    'credentials' => json_encode($newToken),
                    'refresh_token' => $newToken['refresh_token'],
                    'updated_at' => Carbon::now(),
                ]);
            } else {
                $payload = $this->googleInternal->verifyIdToken($decodedToken['id_token']);
            }

            $result['data'] = [
                'name' => $token->name,
                'email' => $payload['email'],
                'status' => 'Access Granted',
                'color' => 'success',
            ];
        } catch (Exception $e) {
            $this->storeLog('system', 'App\Services\AuthService\AuthService@googleAccessStatus', $e->getMessage());
            $result['code'] = 500;
            $result['message'] = 'Terjadi kesalahan server';
            $result['data'] = [
                'name' => '-',
                'email' => '-',
                'status' => 'No Access',
                'color' => 'error'
            ];
        }

        return $result;
    }

    public function revokeAccess() : array
    {
        $result = ['code' => 200, 'message' => 'Berhasil Logout'];
        try {
            auth()->invalidate(true);
        } catch (Exception $e) {
            $this->storeLog(auth()->user()->name, 'App\Services\AuthService\AuthService@revokeAccess', $e->getMessage());
            $result['code'] = 500;
            $result['message'] = 'Gagal Logout';
        }

        return $result;
    }

    public function refreshToken() : array
    {
        $result = ['code' => 200, 'message' => 'Berhasil merefresh access', 'data' => []];
        try {
            $result['data'] = [
                'access_token' => auth()->refresh(),
                'token_type' => 'bearer',
                'user' => [
                    'name' => auth()->user()->name,
                    'photo' => auth()->user()->foto,
                    'role' => auth()->user()->role,
                ]
            ];
        } catch (Exception $e) {
            $this->storeLog(auth()->user()->name, 'App\Services\AuthService\AuthService@refreshToken', $e->getMessage());
            $result['code'] = 500;
            $result['message'] = 'Gagal Logout';
        }

        return $result;
    }

    public function me() : array
    {
        $result = ['code' => 200, 'message' => 'My Profile', 'data' => []];
        try {
            $result['data']['user'] = [
                'name' => auth()->user()->name,
                'photo' => auth()->user()->foto,
                'role' => auth()->user()->role,
            ];
        } catch (Exception $e) {
            $this->storeLog('system', 'App\Services\AuthService\AuthService@me', $e->getMessage());
            $result['code'] = 500;
            $result['message'] = 'Terjadi kesalahan server';
        }

        return $result;
    }
}