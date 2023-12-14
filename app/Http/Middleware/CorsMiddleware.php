<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $allowedOrigins = [getenv('APP_EXTERNAL_URL1'), getenv('APP_EXTERNAL_URL2'), getenv('APP_EXTERNAL_URL3')];
        $headers = [
            'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '86400',
            'Access-Control-Allow-Headers'     => 'Accept, Content-Type, Authorization, X-Requested-With, Is-On-Radius, X-Read-Token'
        ];

        $origin = $request->headers->get('origin');
        if (in_array($origin, $allowedOrigins)) {
            $headers = array_merge($headers, ['Access-Control-Allow-Origin' => $origin]);
        }

        if ($request->isMethod('OPTIONS')) {
            return response()->json('{"method":"OPTIONS"}', 200, $headers);
        }

        $response = $next($request);
        if ($response instanceof StreamedResponse) {
            foreach ($headers as $key => $value) {
                $response->headers->set($key, $value);
            }
        } else {
            foreach ($headers as $key => $value) {
                $response->header($key, $value);
            }
        }

        return $response;
    }
}
