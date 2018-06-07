<?php

namespace Kayrules\authkeepr\Http\Middleware;

use Closure;
use GuzzleHttp\Client;

class AuthKeepr
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if(!env('AUTHKEEPER_SSO_URL')) {
            return response('500 - Missing AUTHKEEPER_SSO_URL.', 401);
        }

        $header = $request->header('Authorization');
        
        $client = new Client();
		$vars = [
			'http_errors' => false,
			'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $header
			],
		];

		$c = $client->request('GET', env('AUTHKEEPER_SSO_URL'), $vars);

		if($c->getStatusCode() != 200) {
            return response('401 - Unauthorized.', 401);
		}
		else {
            $response = (object) json_decode($c->getBody(), true);
            if($response->id > 0) $request->merge(['sso_id' => $response->id]);
            else return response('401 - Unauthorized.', 401);
		}

        return $next($request);
    }
}
